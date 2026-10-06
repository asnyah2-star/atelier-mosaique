// TODO (mission 3) : ajouter les interactions avec jQuery, chargé avant ce fichier.
// TODO (mission 5) : appeler les fonctions de api.js pour les échanges AJAX.
// Le moteur de mosaïque et Canvas peut conserver son JavaScript natif.
// La page de départ fonctionne sans JavaScript.
$(document).ready(function () {
    var envoiImagesEnCours = false;
    // lit ces données pour que le moteur puisse les utiliser.
    var blocDonneesMosaique = document.getElementById('donnees-mosaique');

    try {
        window.donneesMosaiqueProjet = blocDonneesMosaique
            ? JSON.parse(blocDonneesMosaique.textContent)
            : [];
    } catch (erreurLecture) {
        window.donneesMosaiqueProjet = [];
    }

    function afficherResultatsImages(resultats) {
        var liste = document.getElementById('resultats-envoi-images');
        if (!liste) {
            return;
        }

        liste.replaceChildren();
        liste.hidden = false;
        resultats.forEach(function (resultat) {
            var ligne = document.createElement('li');
            var nom = document.createElement('strong');

            nom.textContent = resultat.name + ' : ';
            ligne.appendChild(nom);
            ligne.appendChild(document.createTextNode(
                (resultat.success ? 'acceptée — ' : 'refusée — ') + resultat.message
            ));
            liste.appendChild(ligne);
        });
    }

    function ajouterImageGalerie(resultat, projectId, csrfToken) {
        var liste = document.getElementById('liste-galerie-images');
        var element = document.createElement('li');
        var figure = document.createElement('figure');
        var image = document.createElement('img');
        var legende = document.createElement('figcaption');
        var formulaire = document.createElement('form');

        image.src = 'image.php?id=' + encodeURIComponent(resultat.image_id)
            + '&project_id=' + encodeURIComponent(projectId)
            + '&thumb=1&max=1200';
        image.alt = resultat.name;
        image.loading = 'lazy';
        legende.textContent = resultat.name;
        figure.append(image, legende);

        formulaire.method = 'post';
        [
            ['action', 'demander_retrait'],
            ['csrf_token', csrfToken],
            ['project_id', projectId],
            ['image_id', resultat.image_id]
        ].forEach(function (champ) {
            var entree = document.createElement('input');
            entree.type = 'hidden';
            entree.name = champ[0];
            entree.value = champ[1];
            formulaire.appendChild(entree);
        });

        var boutonRetrait = document.createElement('button');
        boutonRetrait.type = 'submit';
        boutonRetrait.textContent = 'Retirer cette image';
        formulaire.appendChild(boutonRetrait);

        element.append(figure, formulaire);
        liste.prepend(element);
        document.getElementById('galerie-vide').hidden = true;
    }

    $('.formulaire button[type="button"]').on('click', function () {
        document.getElementById('nom').focus();
    });
     $('#form-renommer').on('submit', function (evenement) {
        evenement.preventDefault();

        var formulaire = this;
        var bouton = $('#bouton-renommer');
        var message = document.getElementById('message-renommage');

        var donnees = {
            project_id: formulaire.elements.project_id.value,
            nom: formulaire.elements.nom.value,
            csrf_token: formulaire.elements.csrf_token.value
        };

        bouton.prop('disabled', true);
        message.textContent = 'Renommage en cours…';

        window.apiRenommerProjet(donnees)
            .done(function (reponse) {
                message.textContent = reponse.message;
                formulaire.elements.nom.value = reponse.name;
                document.querySelector('h1').textContent = reponse.name;
                document.title = reponse.name;
            })
            .fail(function (xhr) {
                var reponse = xhr.responseJSON;
                message.textContent = reponse
                    ? reponse.message
                    : 'Le renommage a échoué. Réessaie.';
            })
            .always(function () {
                bouton.prop('disabled', false);
            });
    });   

    var formulaireEnvoiImages = document.getElementById('form-envoi-images');
    var champFichiersImages = formulaireEnvoiImages?.querySelector('input[type="file"]');
    var boutonEnvoiImages = document.getElementById('bouton-envoi-images');

    if (champFichiersImages && boutonEnvoiImages) {
        var actualiserBoutonEnvoiImages = function () {
            var fichiersSelectionnes = champFichiersImages.files.length > 0;
            boutonEnvoiImages.disabled = !fichiersSelectionnes || envoiImagesEnCours;
            boutonEnvoiImages.classList.toggle('button-attention', fichiersSelectionnes);
        };
        champFichiersImages.addEventListener('change', actualiserBoutonEnvoiImages);
        actualiserBoutonEnvoiImages();
    }

    $('#form-envoi-images').on('submit', function (evenement) {
        evenement.preventDefault();
        if (envoiImagesEnCours) {
            return;
        }

        var formulaire = this;
        var bouton = document.getElementById('bouton-envoi-images');
        var message = document.getElementById('message-envoi-images');
        var projectId = formulaire.elements.project_id.value;
        var csrfToken = formulaire.elements.csrf_token.value;
        var formData = new FormData(formulaire);

        envoiImagesEnCours = true;
        bouton.disabled = true;
        bouton.classList.remove('button-attention');
        bouton.textContent = 'Envoi en cours…';
        formulaire.setAttribute('aria-busy', 'true');
        message.textContent = 'Les images sont en cours de vérification et d’envoi…';

        window.apiEnvoyerImages(formData, projectId)
            .done(function (reponse) {
                afficherResultatsImages(reponse.results || []);
                message.textContent = reponse.message;

                (reponse.results || []).forEach(function (resultat) {
                    if (resultat.success) {
                        ajouterImageGalerie(resultat, reponse.project_id, csrfToken);
                        window.donneesMosaiqueProjet.push({
                            src: resultat.src,
                            ar: resultat.ar,
                            name: resultat.name
                        });
                    }
                });
                if ((reponse.results || []).some(function (resultat) { return resultat.success; })) {
                    document.getElementById('exportPng').disabled = false;
                    if (window.rendreMosaiqueProjet) {
                        window.rendreMosaiqueProjet();
                    }
                }

                // Les fichiers refusés pourront être resélectionnés seuls.
                formulaire.querySelector('input[type="file"]').value = '';
            })
            .fail(function (xhr) {
                var reponse = xhr.responseJSON;
                message.textContent = reponse && reponse.message
                    ? reponse.message
                    : 'L’envoi a échoué. Vérifie ta connexion et réessaie.';
            })
            .always(function () {
                envoiImagesEnCours = false;
                bouton.disabled = !champFichiersImages || champFichiersImages.files.length === 0;
                bouton.textContent = 'Envoyer les images';
                bouton.classList.toggle('button-attention', Boolean(champFichiersImages?.files.length));
                formulaire.removeAttribute('aria-busy');
            });
    });



	// Moteur Canvas repris du prototype, avec les images validées du projet.
	if (document.querySelector('.mosaic-tool')) {
	const mosaicForm = document.querySelector('.mosaic-panel');
	const generateButton = mosaicForm?.querySelector('button[type="submit"]');
	if (mosaicForm && generateButton) {
	  const initialSettings = new URLSearchParams(new FormData(mosaicForm)).toString();
	  const signalChangedSettings = () => {
	    const currentSettings = new URLSearchParams(new FormData(mosaicForm)).toString();
	    generateButton.classList.toggle('button-attention', currentSettings !== initialSettings);
	  };
	  mosaicForm.addEventListener('input', signalChangedSettings);
	  mosaicForm.addEventListener('change', signalChangedSettings);
	}

    // ===== Data injectée par PHP  -> =====
	const DATA = window.donneesMosaiqueProjet || [];
  
	const W = Number(document.getElementById("w").value);
	const H = Number(document.getElementById("h").value);
	const GAP = Number(document.getElementById("gap").value);
	const RADIUS = Number(document.getElementById("radius").value);
	const MARGIN_DEFAULT = Number(document.getElementById("margin").value);
  
	const MAX_TILE_W = 0.33 * W;
	const MAX_TILE_H = 0.33 * H;
  
	const MODE = document.querySelector('input[name="mode"]:checked')?.value || 'normal';
	const seedParam = document.getElementById('seed').value.trim();
	const SEED = (seedParam && /^\d+$/.test(seedParam)) ? parseInt(seedParam, 10) : Date.now();
  
	// ===== RNG (seed) =====
	function mulberry32(a){
	  return function(){
		a |= 0; a = a + 0x6D2B79F5 | 0;
		let t = Math.imul(a ^ a >>> 15, 1 | a);
		t ^= t + Math.imul(t ^ t >>> 7, 61 | t);
		return ((t ^ t >>> 14) >>> 0) / 4294967296;
	  }
	}
	const rand = mulberry32(SEED);
	const clamp = (v,a,b)=>Math.max(a,Math.min(b,v));
	const rint  = (a,b)=>Math.floor(a + rand()*(b-a+1));
  
	// ===== Helpers URL thumb =====
	function thumbUrl(src, px){
	  const max = Math.max(120, Math.min(4000, Math.round(px)));
	  const u = new URL(src, location.href);
	  u.searchParams.set('max', String(max));
	  return u.toString();
	}
  
	// ===== Preview background live =====
	function getBgColor(){
	  const bgTxt = document.getElementById('bg');
	  const col = (bgTxt && /^#[0-9a-fA-F]{6}$/.test(bgTxt.value)) ? bgTxt.value : '#12121a';
	  return col;
	}
	function isBgTransparent(){
	  const chk = document.getElementById('bg_transparent');
	  return !!(chk && chk.checked);
	}
  
	function applyPreviewBackgroundLive(){
	  const wrap = document.getElementById('previewWrap');
	  if (!wrap) return;
  
	  const col = getBgColor();
	  const isT = isBgTransparent();
  
	  wrap.style.setProperty('--export-bg', col);
	  wrap.classList.toggle('transparent', isT);
	  wrap.classList.toggle('colored', !isT);
	}
  
function scalePreview(){
	  const viewport = document.getElementById('previewViewport');
	  if (!viewport) return;
	
	  // largeur réellement dispo (le viewport fait 100% du conteneur)
	  const rect = viewport.getBoundingClientRect();
	
	  // un peu de marge pour éviter de coller aux bords
	  const padding = 24;
	  const availableW = Math.max(320, rect.width - padding);
	
	  const scale = Math.min(1, availableW / W);
	
	  // hauteur affichée du viewport = H * scale (sinon ça "débordera" sous le viewport)
	  const pvH = Math.round(H * scale);
	
	  const tool = document.querySelector('.mosaic-tool');
	  tool.style.setProperty('--pv-scale', String(scale));
	  tool.style.setProperty('--pv-h', pvH + 'px');
	}
  
	// ===== Mosaic layout helpers =====
	function pickGrid(){
	  let cols =
		MODE === 'dense' ? 14 :
		MODE === 'aere'  ? 8  :
						  11;
  
	  let cellW = Math.floor((W - GAP*(cols-1)) / cols);
	  cellW = Math.max(12, cellW);
  
	  let rows  = Math.round((H + GAP) / (cellW + GAP));
	  rows = clamp(rows, 8, MODE === 'dense' ? 20 : 14);
  
	  let cellH = Math.floor((H - GAP*(rows-1)) / rows);
	  cellH = Math.max(12, cellH);
  
	  return { cols, rows, cellW, cellH };
	}
  
	const makeOcc = (r,c)=>Array.from({length:r},()=>Array(c).fill(false));
  
	function fits(occ,r,c,rs,cs){
	  if (r+rs>occ.length || c+cs>occ[0].length) return false;
	  for(let y=r;y<r+rs;y++){
		for(let x=c;x<c+cs;x++){
		  if(occ[y][x]) return false;
		}
	  }
	  return true;
	}
  
	function occupy(occ,r,c,rs,cs){
	  for(let y=r;y<r+rs;y++){
		for(let x=c;x<c+cs;x++){
		  occ[y][x]=true;
		}
	  }
	}
  
	function nextEmpty(occ){
	  for(let y=0;y<occ.length;y++){
		for(let x=0;x<occ[0].length;x++){
		  if(!occ[y][x]) return {y,x};
		}
	  }
	  return null;
	}
  
	function pickSpan(maxC,maxR){
	  const bag = [
		[1,1],[1,1],[1,1],[1,1],[1,1],
		[2,1],[2,1],[2,1],
		[1,2],[1,2],[1,2],
		[2,2],[2,2],
		[3,1],[1,3],
		[3,2],[2,3],
		[4,1],[1,4],
		[3,3]
	  ];
  
	  let [cs,rs] = bag[rint(0,bag.length-1)];
	  cs = clamp(cs,1,maxC);
	  rs = clamp(rs,1,maxR);
  
	  if(rand()<0.18){
		if(rand()<0.5){ cs=1; rs=clamp(rint(2,maxR),2,maxR); }
		else          { rs=1; cs=clamp(rint(2,maxC),2,maxC); }
	  }
	  return {cs,rs};
	}
  
	// ===== Render mosaic =====
	function renderPinterest(){
	  const g = document.getElementById('gallery');
	  if (!g) return;
	  g.innerHTML = '';
  
	  const { cols, rows, cellW, cellH } = pickGrid();
  
	  const maxCS = Math.max(1, Math.floor(MAX_TILE_W / (cellW + GAP)));
	  const maxRS = Math.max(1, Math.floor(MAX_TILE_H / (cellH + GAP)));
  
	  // Grid CSS
	  g.style.display = 'grid';
	  g.style.gap = GAP + 'px';
	  g.style.gridAutoFlow = 'dense';
	  g.style.gridTemplateColumns = `repeat(${cols}, ${cellW}px)`;
	  g.style.gridTemplateRows = `repeat(${rows}, ${cellH}px)`;
	  g.style.overflow = 'hidden';
  
	  const occ = makeOcc(rows, cols);
  
	  const items = DATA.slice().sort(() => rand() - 0.5);
	  if (!items.length) return;
  
	  const dpr = window.devicePixelRatio || 1;
	  let imgIdx = 0;
  
	  while (true) {
		const spot = nextEmpty(occ);
		if (!spot) break;
  
		let chosen = null;
		for (let tries = 0; tries < 30; tries++){
		  const s = pickSpan(maxCS, maxRS);
		  if (fits(occ, spot.y, spot.x, s.rs, s.cs)) { chosen = s; break; }
		}
		if (!chosen) chosen = { cs: 1, rs: 1 };
  
		occupy(occ, spot.y, spot.x, chosen.rs, chosen.cs);
  
		const it = items[imgIdx % items.length];
		imgIdx++;
  
		const pxW = chosen.cs * cellW + (chosen.cs - 1) * GAP;
		const pxH = chosen.rs * cellH + (chosen.rs - 1) * GAP;
		const need = Math.max(pxW, pxH) * dpr;
  
		const tile = document.createElement('div');
		tile.className = 'mosaic-tile';
		tile.style.gridColumn = `${spot.x + 1} / span ${chosen.cs}`;
		tile.style.gridRow    = `${spot.y + 1} / span ${chosen.rs}`;
  
		const img = document.createElement('img');
		img.alt = it.name || '';
		img.loading = 'lazy';
		img.src = thumbUrl(it.src, need);
  
		// object-position “intelligent”
		const ar = it.ar || 1;
		img.style.objectPosition = (ar < 0.9) ? '50% 35%' : (ar > 1.6) ? '50% 50%' : '50% 45%';
  
		img.addEventListener('load', () => tile.classList.add('loaded'), { once: true });
  
		tile.appendChild(img);
		g.appendChild(tile);
	  }
	}
  
	// ===== Seed regen =====
	function regenSeed(){
	  const u = new URL(location.href);
	  u.searchParams.set('seed', String(Date.now()));
	  location.href = u.toString();
	}
  
	// ===== Export helpers =====
	function parseObjectPosition(pos){
	  let bx = 0.5, by = 0.45;
	  if (!pos) return {bx, by};
	  const parts = pos.trim().split(/\s+/);
	  const px = parts[0] || '50%';
	  const py = parts[1] || '50%';
  
	  const toBias = (p, fallback) => {
		if (p.endsWith('%')) return clamp(parseFloat(p)/100, 0, 1);
		if (p === 'left' || p === 'top') return 0;
		if (p === 'center') return 0.5;
		if (p === 'right' || p === 'bottom') return 1;
		return fallback;
	  };
	  return { bx: toBias(px, 0.5), by: toBias(py, 0.45) };
	}
  
	function coverCropDraw(ctx, img, dx, dy, dw, dh, biasX = 0.5, biasY = 0.45){
	  const sw = img.naturalWidth || img.width;
	  const sh = img.naturalHeight || img.height;
	  if (!sw || !sh) return;
  
	  const scale = Math.max(dw / sw, dh / sh);
	  const rw = sw * scale;
	  const rh = sh * scale;
  
	  const ox = (dw - rw) * biasX;
	  const oy = (dh - rh) * biasY;
  
	  ctx.drawImage(img, dx + ox, dy + oy, rw, rh);
	}
  
	function roundedRectPath(ctx, x, y, w, h, r){
	  const rr = clamp(r, 0, Math.min(w, h) / 2);
	  if (typeof ctx.roundRect === 'function') {
		ctx.beginPath();
		ctx.roundRect(x, y, w, h, rr);
		return;
	  }
	  ctx.beginPath();
	  ctx.moveTo(x + rr, y);
	  ctx.arcTo(x + w, y, x + w, y + h, rr);
	  ctx.arcTo(x + w, y + h, x, y + h, rr);
	  ctx.arcTo(x, y + h, x, y, rr);
	  ctx.arcTo(x, y, x + w, y, rr);
	  ctx.closePath();
	}
  
	async function exportMosaicToPng(){
	  const el = document.getElementById('gallery');
	  const bgTxt = document.getElementById('bg');
	  const chk = document.getElementById('bg_transparent');
	  const marginInput = document.getElementById('margin');
	  if (!el || !bgTxt || !chk || !marginInput) return;
  
	  const m = Math.max(0, Math.min(800, parseInt(marginInput.value || String(MARGIN_DEFAULT), 10) || 0));
  
	  const outW = W + 2*m;
	  const outH = H + 2*m;
  
	  const canvas = document.createElement('canvas');
	  canvas.width = outW;
	  canvas.height = outH;
	  const ctx = canvas.getContext('2d');
  
	  ctx.setTransform(1,0,0,1,0,0);
	  ctx.clearRect(0,0,outW,outH);
  
	  const bgColor = /^#[0-9a-fA-F]{6}$/.test(bgTxt.value) ? bgTxt.value : '#12121a';
	  const transparent = chk.checked;
  
	  if (!transparent) {
		ctx.fillStyle = bgColor;
		ctx.fillRect(0,0,outW,outH);
	  }
  
	  // IMPORTANT: coords basées sur layout non-scalé (W×H), donc scaleX=1 si getBoundingClientRect est stable
	  // Mais comme on applique un transform scale sur #gallery, son rect est scalé => on évite le piège :
	  // On mesure les tuiles via offsetLeft/Top + offsetWidth/Height (non affectés par transform du parent).
	  const tiles = Array.from(el.querySelectorAll('.mosaic-tile'));
  
	  // preload images
	  const imgs = tiles.map(t => t.querySelector('img')).filter(Boolean);
	  await Promise.all(imgs.map(img => new Promise(res => {
		if (img.complete && img.naturalWidth) return res();
		img.addEventListener('load', () => res(), { once: true });
		img.addEventListener('error', () => res(), { once: true });
	  })));
  
	  for (const tile of tiles){
		const img = tile.querySelector('img');
		if (!img || !img.naturalWidth) continue;
  
		// ✅ coords non affectées par transform scale du #gallery :
		const x = tile.offsetLeft + m;
		const y = tile.offsetTop + m;
		const w = tile.offsetWidth;
		const h = tile.offsetHeight;
  
		const {bx, by} = parseObjectPosition(img.style.objectPosition);
  
		// radius en px export (pas besoin de scale)
		const rad = RADIUS;
  
		ctx.save();
		roundedRectPath(ctx, x, y, w, h, rad);
		ctx.clip();
		coverCropDraw(ctx, img, x, y, w, h, bx, by);
		ctx.restore();
	  }
  
	  const bgTag = transparent ? 'transparent' : bgColor.replace('#','');
	  const a = document.createElement('a');
	  a.href = canvas.toDataURL('image/png');
	  a.download = `mosaic_${W}x${H}_m-${m}_mode-${MODE}_seed-${SEED}_gap-${GAP}_rad-${RADIUS}_bg-${bgTag}.png`;
	  a.click();
	}
  
	// ===== Setup UI events =====
	function setupBgControls(){
	  const color = document.getElementById('bg_color');
	  const bgTxt = document.getElementById('bg');
	  const chk = document.getElementById('bg_transparent');
  
	  if (color && bgTxt) {
		color.addEventListener('input', () => {
		  bgTxt.value = color.value;
		  applyPreviewBackgroundLive();
		});
		bgTxt.addEventListener('input', () => {
		  if (/^#[0-9a-fA-F]{6}$/.test(bgTxt.value)) color.value = bgTxt.value;
		  applyPreviewBackgroundLive();
		});
	  }
	  if (chk) chk.addEventListener('change', applyPreviewBackgroundLive);
  
	  applyPreviewBackgroundLive();
	}

	$('#saveMosaicSettings').on('click', function () {
	  const form = document.querySelector('.mosaic-panel');
	  const button = this;
	  const message = document.getElementById('mosaic-message');
	  if (!form || !message) return;
	  if (!form.reportValidity()) return;

	  const mode = form.querySelector('input[name="mode"]:checked');
	  const data = {
	    project_id: form.elements.project_id.value,
	    csrf_token: document.getElementById('mosaicCsrfToken').value,
	    w: form.elements.w.value,
	    h: form.elements.h.value,
	    mode: mode ? mode.value : '',
	    gap: form.elements.gap.value,
	    radius: form.elements.radius.value,
	    bg: form.elements.bg.value,
	    bg_transparent: form.elements.bg_transparent.checked,
	    margin: form.elements.margin.value,
	    seed: form.elements.seed.value.trim()
	  };

	  button.disabled = true;
	  form.setAttribute('aria-busy', 'true');
	  message.textContent = 'Enregistrement des réglages…';

	  window.apiEnregistrerReglagesMosaique(data)
	    .done(function (response) {
	      // Le moteur lit ses dimensions au démarrage. Revenir à l’URL canonique
	      // recharge les valeurs confirmées par PHP, sans anciens paramètres GET.
	      message.textContent = (response.message || 'Réglages enregistrés.') + ' Rechargement de l’aperçu…';
	      window.setTimeout(function () {
	        window.location.assign('projet.php?id=' + encodeURIComponent(data.project_id));
	      }, 2000);
	    })
	    .fail(function (xhr) {
	      const response = xhr.responseJSON;
	      message.textContent = response && response.message
	        ? response.message
	        : 'Les réglages n’ont pas pu être enregistrés. Vérifie ta connexion et réessaie.';
	      button.disabled = false;
	      form.removeAttribute('aria-busy');
	    });
	});

	// ===== Boot =====
	const tool = document.querySelector('.mosaic-tool');
	tool.style.setProperty('--W', W + 'px');
	tool.style.setProperty('--H', H + 'px');
	tool.style.setProperty('--gap', GAP + 'px');
	tool.style.setProperty('--radius', RADIUS + 'px');
	setupBgControls();
	renderPinterest();
	window.rendreMosaiqueProjet = renderPinterest;
	scalePreview();
	window.addEventListener('resize', () => requestAnimationFrame(scalePreview));
	document.getElementById('gallery')?.addEventListener('dblclick', regenSeed);
	document.getElementById('regenMosaic')?.addEventListener('click', regenSeed);
	document.getElementById('exportPng')?.addEventListener('click', exportMosaicToPng);
    }
  

});
