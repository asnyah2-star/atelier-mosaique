<?php
declare(strict_types=1);

/**
 * index.php — Mosaïque Pinterest configurable + export PNG (canvas)
 * Images: ./IMG_MOSA/
 * Thumbs: ?thumb=1&src=<relpath>&max=1200
 */

$SRC_DIR = realpath(__DIR__ . '/IMG_MOSA');
if (!$SRC_DIR || !is_dir($SRC_DIR)) {
  http_response_code(500);
  exit("Dossier manquant: ./IMG_MOSA");
}

$ALLOWED = ['jpg','jpeg','png','webp','gif'];

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function gdOk(): bool { return extension_loaded('gd') && function_exists('imagecreatetruecolor'); }

function listImages(string $dir, array $allowedExt): array {
  $out = [];
  foreach (new DirectoryIterator($dir) as $f) {
	if ($f->isDot() || !$f->isFile()) continue;
	$ext = strtolower($f->getExtension());
	if (!in_array($ext, $allowedExt, true)) continue;
	$out[] = $f->getPathname();
  }
  shuffle($out);
  return $out;
}

function loadGdImage(string $path, string $mime) {
  return match ($mime) {
	'image/jpeg' => @imagecreatefromjpeg($path),
	'image/png'  => @imagecreatefrompng($path),
	'image/gif'  => @imagecreatefromgif($path),
	'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
	default => null
  };
}

function safeJoin(string $baseReal, string $rel): ?string {
  $rel = str_replace(["\0", "\\", "../", "..\\"], '', $rel);
  $abs = realpath($baseReal . '/' . $rel);
  if (!$abs) return null;
  if (strpos($abs, $baseReal . DIRECTORY_SEPARATOR) !== 0 && $abs !== $baseReal) return null;
  return $abs;
}

/* ====== Params ====== */
$W = max(320, min(8000, (int)($_GET['w'] ?? 1400)));
$H = max(240, min(8000, (int)($_GET['h'] ?? 900)));

$mode = (string)($_GET['mode'] ?? 'normal');
if (!in_array($mode, ['dense','normal','aere'], true)) $mode = 'normal';

$seedRaw = (string)($_GET['seed'] ?? '');
$seed = (ctype_digit($seedRaw) && $seedRaw !== '') ? (int)$seedRaw : null;

$gap = (int)($_GET['gap'] ?? 8);
$gap = max(0, min(60, $gap));

$radius = (int)($_GET['radius'] ?? 12);
$radius = max(0, min(120, $radius));

$margin = (int)($_GET['margin'] ?? 0);
$margin = max(0, min(800, $margin));

$bg = (string)($_GET['bg'] ?? '#12121a');
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $bg)) $bg = '#12121a';

$bg_transparent = ((string)($_GET['bg_transparent'] ?? '') === '1');

/* ====== Thumbs endpoint ====== */
if (isset($_GET['thumb'])) {
  if (!gdOk()) { http_response_code(500); exit("GD requis (php-gd)"); }

  $rel = (string)($_GET['src'] ?? '');
  $max = max(50, min(4000, (int)($_GET['max'] ?? 1200)));

  $srcAbs = safeJoin($SRC_DIR, $rel);
  if (!$srcAbs || !is_file($srcAbs)) { http_response_code(404); exit("Not found"); }

  $info = @getimagesize($srcAbs);
  if (!$info) { http_response_code(415); exit("Bad image"); }
  [$sw,$sh] = $info;
  $mime = $info['mime'] ?? '';
  if ($sw < 2 || $sh < 2) { http_response_code(415); exit("Bad size"); }

  $srcIm = loadGdImage($srcAbs, $mime);
  if (!$srcIm) { http_response_code(415); exit("Unsupported"); }

  $scale = min(1.0, $max / max($sw, $sh)); // no upscale
  $tw = max(1, (int)round($sw * $scale));
  $th = max(1, (int)round($sh * $scale));

  $dstIm = imagecreatetruecolor($tw, $th);
  $white = imagecolorallocate($dstIm, 255,255,255);
  imagefill($dstIm, 0,0, $white);
  imagecopyresampled($dstIm, $srcIm, 0,0, 0,0, $tw,$th, $sw,$sh);

  header('Content-Type: image/jpeg');
  header('Cache-Control: public, max-age=604800, immutable');
  imageinterlace($dstIm, true);
  imagejpeg($dstIm, null, 85);

  imagedestroy($srcIm);
  imagedestroy($dstIm);
  exit;
}

/* ====== Build DATA ====== */
$files = listImages($SRC_DIR, $ALLOWED);
$files = array_slice($files, 0, 700);

$data = [];
foreach ($files as $abs) {
  $info = @getimagesize($abs);
  if (!$info) continue;
  [$iw,$ih] = $info;
  if ($iw < 2 || $ih < 2) continue;

  $rel = str_replace($SRC_DIR . DIRECTORY_SEPARATOR, '', $abs);
  $data[] = [
	'src'  => $rel,
	'ar'   => $iw / $ih,
	'name' => basename($rel),
  ];
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mosaïque Pinterest</title>
  <style>
:root{
	--W: <?= (int)$W ?>px;
	--H: <?= (int)$H ?>px;
	--gap: <?= (int)$gap ?>px;
	--radius: <?= (int)$radius ?>px;
	--export-bg: <?= h($bg) ?>;
  
	/* preview scaling vars */
	--pv-scale: 1;
	--pv-w: var(--W);
	--pv-h: var(--H);
	--pv-tx: 0px;
  
	--ui-bg: #0b0b0e;
	--ui-card: rgba(255,255,255,.04);
	--ui-line: rgba(255,255,255,.12);
	--ui-txt: #eaeaf2;
	--ui-muted: rgba(234,234,242,.72);
  }
  
  *{ box-sizing:border-box; }
  
  body{
	margin:0;
	padding:16px;
	background: var(--ui-bg);
	color: var(--ui-txt);
	font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
  }
  
  /* ===== Panel / Form UI ===== */
  .panel{
	display:flex;
	gap:12px;
	align-items:flex-end;
	flex-wrap:wrap;
	margin-bottom:12px;
	padding:12px;
	border:1px solid var(--ui-line);
	border-radius:14px;
	background: var(--ui-card);
  }
  
  .field{
	display:flex;
	flex-direction:column;
	gap:6px;
	font-size:13px;
	color: var(--ui-muted);
  }
  .field label{
	font-size:12px;
	color: var(--ui-muted);
  }
  
  input[type="number"], input[type="text"]{
	width: 140px;
	padding: 8px 10px;
	border-radius: 10px;
	border: 1px solid var(--ui-line);
	background: rgba(0,0,0,.25);
	color: var(--ui-txt);
	outline: none;
  }
  input[type="number"]:focus, input[type="text"]:focus{
	border-color: rgba(184,184,255,.5);
  }
  
  .radios,
  .inlineBox{
	display:flex;
	gap:10px;
	align-items:center;
	padding: 8px 10px;
	border-radius: 10px;
	border: 1px solid var(--ui-line);
	background: rgba(0,0,0,.18);
  }
  .radios label{
	display:flex;
	gap:6px;
	align-items:center;
	cursor:pointer;
	color: var(--ui-txt);
	font-size:13px;
  }
  
  .btn{
	appearance:none;
	border: 1px solid var(--ui-line);
	background: rgba(255,255,255,.06);
	color: var(--ui-txt);
	padding: 9px 12px;
	border-radius: 10px;
	cursor:pointer;
	font-size: 13px;
	white-space: nowrap;
  }
  .btn:hover{ background: rgba(255,255,255,.10); }
  .btn.primary{
	border-color: rgba(184,184,255,.35);
	background: rgba(184,184,255,.12);
  }
  
  .hint{
	margin: 6px 0 12px;
	font-size: 13px;
	color: var(--ui-muted);
  }
  

  

  

  

 /* ===== Preview container (stable, no drift) ===== */
 .preview-center{
   width: 100%;
   margin-top: 12px;
 }
 
 /* le viewport prend la largeur dispo et coupe proprement */
 #previewViewport{
   width: 100%;
   height: var(--pv-h);     /* hauteur affichée = H*scale */
   overflow: hidden;
   position: relative;
 }
 
 /* on scale le WRAP lui-même et on le centre via transform */
 #previewWrap{
   position: absolute;
   top: 0;
   left: 50%;
 
   width: var(--W);
   height: var(--H);
 
   border-radius: 14px;
   overflow: hidden;
   box-shadow: 0 10px 30px rgba(0,0,0,.35);
 
   transform-origin: top left;
transform: scale(var(--pv-scale)) translateX(-50%);
 }
 
 /* background preview */
 #previewWrap.colored{ background: var(--export-bg); }
 #previewWrap.transparent{
   background:
	 linear-gradient(45deg, rgba(255,255,255,.08) 25%, transparent 25%),
	 linear-gradient(-45deg, rgba(255,255,255,.08) 25%, transparent 25%),
	 linear-gradient(45deg, transparent 75%, rgba(255,255,255,.08) 75%),
	 linear-gradient(-45deg, transparent 75%, rgba(255,255,255,.08) 75%);
   background-size: 24px 24px;
   background-position: 0 0, 0 12px, 12px -12px, -12px 0px;
 }
 
 /* plus besoin de galleryPos */
 #galleryPos{
   position: static;
   width: 100%;
   height: 100%;
 }
 
 /* gallery remplit le wrap, PAS de transform ici */
 #gallery{
   width: 100%;
   height: 100%;
   overflow: hidden;
   user-select: none;
 } 

  

  /* ===== Tiles ===== */
  .tile{
	position:relative;
	overflow:hidden;
	border-radius: var(--radius);
	background: rgba(0,0,0,.25);
	transform: translateZ(0);
  }
  
  .tile img{
	width:100%;
	height:100%;
	object-fit:cover;
	display:block;
	opacity:0;
	transform: scale(1.02);
	transition: transform .35s ease, opacity .35s ease;
	filter: saturate(1.03) contrast(1.02);
  }
  
  .tile.loaded img{ opacity:1; }
  .tile:hover img{ transform: scale(1.08); }
  </style>
</head>
<body>

  <form class="panel" method="get" action="">
	<div class="field">
	  <label for="w">Largeur (px)</label>
	  <input id="w" name="w" type="number" min="320" max="8000" value="<?= (int)$W ?>">
	</div>

	<div class="field">
	  <label for="h">Hauteur (px)</label>
	  <input id="h" name="h" type="number" min="240" max="8000" value="<?= (int)$H ?>">
	</div>

	<div class="field">
	  <label>Mode</label>
	  <div class="radios" role="radiogroup" aria-label="Mode">
		<label><input type="radio" name="mode" value="dense"  <?= $mode==='dense'?'checked':''; ?>> dense</label>
		<label><input type="radio" name="mode" value="normal" <?= $mode==='normal'?'checked':''; ?>> normal</label>
		<label><input type="radio" name="mode" value="aere"   <?= $mode==='aere'?'checked':''; ?>> aéré</label>
	  </div>
	</div>

	<div class="field">
	  <label for="seed">Seed (optionnel)</label>
	  <input id="seed" name="seed" type="text" inputmode="numeric" placeholder="auto" value="<?= $seed !== null ? (string)$seed : '' ?>">
	</div>

	<div class="field">
	  <label for="gap">Espacement (px)</label>
	  <input id="gap" name="gap" type="number" min="0" max="60" value="<?= (int)$gap ?>">
	</div>

	<div class="field">
	  <label for="radius">Radius (px)</label>
	  <input id="radius" name="radius" type="number" min="0" max="120" value="<?= (int)$radius ?>">
	</div>
	
	<div class="field">
	  <label for="margin">Marge export (px)</label>
	  <input id="margin" name="margin" type="number" min="0" max="800" value="<?= (int)$margin ?>">
	</div>

	<div class="field">
	  <label for="bg">Background</label>
	  <input id="bg" name="bg" type="text" value="<?= h($bg) ?>" placeholder="#12121a">
	</div>

	<div class="field">
	  <label>&nbsp;</label>
	  <div class="inlineBox">
		<input id="bg_color" type="color" value="<?= h($bg) ?>" style="width:44px; height:32px; padding:0; border:0; background:transparent; cursor:pointer;">
		<label style="display:flex; gap:6px; align-items:center; cursor:pointer; color: var(--ui-txt); font-size:13px; margin:0;">
		  <input id="bg_transparent" type="checkbox" name="bg_transparent" value="1" <?= $bg_transparent ? 'checked' : '' ?>>
		  transparent
		</label>
	  </div>
	</div>

	<button class="btn primary" type="submit">Générer</button>
	<button class="btn" type="button" id="exportPng">Exporter PNG</button>
  </form>

  <div class="hint">
	Double-clic sur la mosaïque = régénérer (seed auto). Le preview reflète le background (couleur ou damier).
  </div>
  <div class="preview-center">
	<div id="previewViewport">
	  <div class="wrap <?= $bg_transparent ? 'transparent' : 'colored' ?>" id="previewWrap">
		<div id="galleryPos">
		  <div id="gallery"></div>
		</div>
	  </div>
	</div>
  </div>
<script>
	// ===== Data injectée par PHP =====
	const DATA = <?= json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?>;
  
	const W = <?= (int)$W ?>;
	const H = <?= (int)$H ?>;
	const GAP = <?= (int)$gap ?>;
	const RADIUS = <?= (int)$radius ?>;
	const MARGIN_DEFAULT = <?= (int)$margin ?>;
  
	const MAX_TILE_W = 0.33 * W;
	const MAX_TILE_H = 0.33 * H;
  
	const params = new URLSearchParams(location.search);
	const MODE = params.get('mode') || 'normal';   // dense | normal | aere
	const seedParam = params.get('seed');
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
	function thumbUrl(rel, px){
	  const max = Math.max(120, Math.min(4000, Math.round(px)));
	  const u = new URL(location.href);
	  u.searchParams.set('thumb','1');
	  u.searchParams.set('src',rel);
	  u.searchParams.set('max',String(max));
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
	
	  document.documentElement.style.setProperty('--pv-scale', String(scale));
	  document.documentElement.style.setProperty('--pv-h', pvH + 'px');
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
		tile.className = 'tile';
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
	  const tiles = Array.from(el.querySelectorAll('.tile'));
  
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
  
	// ===== Boot =====
	document.addEventListener('DOMContentLoaded', () => {
	  setupBgControls();
	  renderPinterest();
	  
	  scalePreview();
	  window.addEventListener('resize', () => requestAnimationFrame(scalePreview));
  
	  document.getElementById('gallery')?.addEventListener('dblclick', regenSeed);
	  document.getElementById('exportPng')?.addEventListener('click', exportMosaicToPng);
	});
  </script>

</body>
</html>

