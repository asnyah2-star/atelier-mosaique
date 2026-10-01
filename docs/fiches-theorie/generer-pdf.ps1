$ErrorActionPreference = 'Stop'
$dossier = $PSScriptRoot
$encodageTexte = [System.Text.Encoding]::GetEncoding(1252)
[System.Threading.Thread]::CurrentThread.CurrentCulture = [System.Globalization.CultureInfo]::InvariantCulture

function Ajouter-LigneTexte {
    param([System.Collections.Generic.List[object]]$Liste, [string]$Texte, [string]$Police, [double]$Taille, [double]$Interligne, [string]$Couleur)
    $longueur = [Math]::Max(24, [int](490 / ($Taille * 0.51)))
    $mots = $Texte -split '\s+'
    $ligne = ''
    foreach ($mot in $mots) {
        if ($ligne.Length -gt 0 -and ($ligne.Length + $mot.Length + 1) -gt $longueur) {
            $Liste.Add([pscustomobject]@{ Texte = $ligne; Police = $Police; Taille = $Taille; Interligne = $Interligne; Couleur = $Couleur })
            $ligne = $mot
        } else {
            $ligne = if ($ligne.Length) { "$ligne $mot" } else { $mot }
        }
    }
    if ($ligne.Length) { $Liste.Add([pscustomobject]@{ Texte = $ligne; Police = $Police; Taille = $Taille; Interligne = $Interligne; Couleur = $Couleur }) }
}

function Convertir-HtmlEnLignes {
    param([string]$Chemin)
    $html = [System.IO.File]::ReadAllText($Chemin, [System.Text.Encoding]::UTF8)
    $html = [regex]::Replace($html, '(?is)<head.*?</head>', '')
    $html = [regex]::Replace($html, '(?i)<h1[^>]*>', "`n@@TITRE@@")
    $html = [regex]::Replace($html, '(?i)</h1>', "`n")
    $html = [regex]::Replace($html, '(?i)<h2[^>]*>', "`n@@SOUS_TITRE@@")
    $html = [regex]::Replace($html, '(?i)</h2>', "`n")
    $html = [regex]::Replace($html, '(?i)<h3[^>]*>', "`n@@INTERTITRE@@")
    $html = [regex]::Replace($html, '(?i)</h3>', "`n")
    $html = [regex]::Replace($html, '(?i)<p class="eyebrow"[^>]*>', "`n@@PETIT@@")
    $html = [regex]::Replace($html, '(?i)<p class="lead"[^>]*>', "`n@@INTRO@@")
    $html = [regex]::Replace($html, '(?i)<p class="footer"[^>]*>', "`n@@PIED@@")
    $html = [regex]::Replace($html, '(?i)<li[^>]*>', "`n- ")
    $html = [regex]::Replace($html, '(?i)</li>', '')
    $html = [regex]::Replace($html, '(?i)</?(p|div|table|tr|ul|ol|h[1-6])[^>]*>', "`n")
    $html = [regex]::Replace($html, '(?i)</?(td|th)[^>]*>', '  |  ')
    $html = [regex]::Replace($html, '(?i)<br\s*/?>', "`n")
    $html = [regex]::Replace($html, '<[^>]+>', '')
    $html = [System.Net.WebUtility]::HtmlDecode($html)
    $html = $html -replace '&nbsp;', ' '
    $resultat = [System.Collections.Generic.List[object]]::new()
    foreach ($brut in ($html -split "`r?`n")) {
        $texte = ($brut -replace '\s+', ' ').Trim()
        if (-not $texte) { continue }
        if ($texte.StartsWith('@@TITRE@@')) {
            Ajouter-LigneTexte $resultat ($texte.Substring(9)) 'F2' 24 29 '#173D52'
        } elseif ($texte.StartsWith('@@SOUS_TITRE@@')) {
            $resultat.Add([pscustomobject]@{ Texte = ''; Police = 'F1'; Taille = 4; Interligne = 7; Couleur = '#FFFFFF' })
            Ajouter-LigneTexte $resultat ($texte.Substring(14)) 'F2' 15 19 '#14656B'
        } elseif ($texte.StartsWith('@@INTERTITRE@@')) {
            Ajouter-LigneTexte $resultat ($texte.Substring(14)) 'F2' 11.5 15 '#314A62'
        } elseif ($texte.StartsWith('@@PETIT@@')) {
            Ajouter-LigneTexte $resultat ($texte.Substring(9)) 'F2' 8.5 12 '#BB612F'
        } elseif ($texte.StartsWith('@@INTRO@@')) {
            Ajouter-LigneTexte $resultat ($texte.Substring(9)) 'F1' 13 17 '#44536A'
        } elseif ($texte.StartsWith('@@PIED@@')) {
            Ajouter-LigneTexte $resultat ($texte.Substring(8)) 'F1' 8.5 11 '#657184'
        } else {
            Ajouter-LigneTexte $resultat $texte 'F1' 10.5 14 '#202535'
        }
    }
    return ,$resultat
}

function Vers-Rgb {
    param([string]$Hex)
    $h = $Hex.TrimStart('#')
    return ('{0:F3} {1:F3} {2:F3}' -f ([Convert]::ToInt32($h.Substring(0,2),16)/255), ([Convert]::ToInt32($h.Substring(2,2),16)/255), ([Convert]::ToInt32($h.Substring(4,2),16)/255))
}

function Creer-Pdf {
    param([string]$HtmlPath, [string]$PdfPath)
    $lignes = Convertir-HtmlEnLignes $HtmlPath
    $pages = [System.Collections.Generic.List[object]]::new()
    $page = [System.Collections.Generic.List[object]]::new()
    $y = 790.0
    foreach ($ligne in $lignes) {
        if (($y - $ligne.Interligne) -lt 45) {
            $pages.Add($page)
            $page = [System.Collections.Generic.List[object]]::new()
            $y = 790.0
        }
        $page.Add([pscustomobject]@{ Texte = $ligne.Texte; Police = $ligne.Police; Taille = $ligne.Taille; X = 50.0; Y = $y; Couleur = $ligne.Couleur })
        $y -= $ligne.Interligne
    }
    if ($page.Count) { $pages.Add($page) }

    $objets = [System.Collections.Generic.List[string]]::new()
    $objets.Add('<< /Type /Catalog /Pages 2 0 R >>')
    $idsPages = for ($i = 0; $i -lt $pages.Count; $i++) { 5 + (2 * $i) }
    $kids = ($idsPages | ForEach-Object { "$_ 0 R" }) -join ' '
    $objets.Add("<< /Type /Pages /Kids [$kids] /Count $($pages.Count) >>")
    $objets.Add('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>')
    $objets.Add('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>')
    for ($i = 0; $i -lt $pages.Count; $i++) {
        $idPage = 5 + (2 * $i)
        $idContenu = $idPage + 1
        $objets.Add("<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents $idContenu 0 R >>")
        $commandes = [System.Collections.Generic.List[string]]::new()
        foreach ($entree in $pages[$i]) {
            $octets = $encodageTexte.GetBytes($entree.Texte)
            $hex = [System.BitConverter]::ToString($octets).Replace('-', '')
            $police = if ($entree.Police -eq 'F2') { 'F2' } else { 'F1' }
            $rgb = Vers-Rgb $entree.Couleur
            $commandes.Add("BT /$police $($entree.Taille) Tf $rgb rg 1 0 0 1 $($entree.X) $($entree.Y) Tm <$hex> Tj ET")
        }
        $contenu = $commandes -join "`n"
        $longueur = [System.Text.Encoding]::ASCII.GetByteCount($contenu)
        $objets.Add("<< /Length $longueur >>`nstream`n$contenu`nendstream")
    }

    $flux = [System.IO.MemoryStream]::new()
    $entete = [System.Text.Encoding]::ASCII.GetBytes("%PDF-1.4`n")
    $flux.Write($entete, 0, $entete.Length)
    $offsets = [System.Collections.Generic.List[long]]::new()
    for ($i = 0; $i -lt $objets.Count; $i++) {
        $offsets.Add($flux.Position)
        $bloc = "$(($i+1)) 0 obj`n$($objets[$i])`nendobj`n"
        $octets = [System.Text.Encoding]::ASCII.GetBytes($bloc)
        $flux.Write($octets, 0, $octets.Length)
    }
    $xref = $flux.Position
    $table = "xref`n0 $($objets.Count + 1)`n0000000000 65535 f `n"
    foreach ($offset in $offsets) { $table += ('{0:D10} 00000 n ' -f $offset) + "`n" }
    $table += "trailer`n<< /Size $($objets.Count + 1) /Root 1 0 R >>`nstartxref`n$xref`n%%EOF`n"
    $octets = [System.Text.Encoding]::ASCII.GetBytes($table)
    $flux.Write($octets, 0, $octets.Length)
    [System.IO.File]::WriteAllBytes($PdfPath, $flux.ToArray())
    $flux.Dispose()
    Write-Output ("Created: {0} ({1} pages)" -f [System.IO.Path]::GetFileName($PdfPath), $pages.Count)
}

Get-ChildItem -LiteralPath $dossier -Filter '*.html' | Sort-Object Name | ForEach-Object {
    $pdf = [System.IO.Path]::ChangeExtension($_.FullName, '.pdf')
    Creer-Pdf $_.FullName $pdf
}
