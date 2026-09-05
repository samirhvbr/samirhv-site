<?php

/*
| Regenerates the social card at samirhv/public/img/og-card.png.
|
|   php tools/make-og-card.php samirhv/public/img/og-card.png
|
| Drawn with GD rather than rendered from SVG: ImageMagick's built-in SVG
| renderer silently dropped the <tspan> in the wordmark and both gradients, and
| a card that fails quietly is worse than no card. Requires ext-gd and the
| DejaVu fonts (fonts-dejavu-core on Debian).
|
| The image is committed, so this script only runs when the card should change.
| Palette from samirhv/public/vendor/canvas/css/custom.css.
*/
const W = 1200, H = 630;
$F_BOLD = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
$F_REG  = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
$F_MONO = '/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf';

$im = imagecreatetruecolor(W, H);
imagealphablending($im, true);

$rgb = fn (string $hex) => [hexdec(substr($hex,1,2)), hexdec(substr($hex,3,2)), hexdec(substr($hex,5,2))];
$c   = function (string $hex, int $alpha = 0) use ($im, $rgb) { [$r,$g,$b] = $rgb($hex); return imagecolorallocatealpha($im, $r,$g,$b, $alpha); };

imagefilledrectangle($im, 0, 0, W, H, $c('#0a0b10'));

// Indigo aura, top-left. Computed per pixel on a small canvas and then scaled
// up: stacking translucent discs accumulates alpha and drowns the card, which
// is exactly what the first attempt did.
$SW = 150; $SH = 79;
$small = imagecreatetruecolor($SW, $SH);
[$br,$bg,$bb] = $rgb('#0a0b10');
[$ar,$ag,$ab] = $rgb('#6366f1');
$cx = 0.17 * $SW; $cy = 0.16 * $SH; $rad = 0.62 * $SW;
for ($y = 0; $y < $SH; $y++) {
    for ($x = 0; $x < $SW; $x++) {
        $dx = ($x - $cx); $dy = ($y - $cy) * ($SW / $SH) * ($SH / $SW) * 1.9;
        $d = sqrt($dx * $dx + $dy * $dy) / $rad;
        $t = max(0.0, 1.0 - $d);
        $k = $t * $t * 0.30;                    // peak 30% accent at the centre
        imagesetpixel($small, $x, $y, imagecolorallocate($small,
            (int) round($br + ($ar - $br) * $k),
            (int) round($bg + ($ag - $bg) * $k),
            (int) round($bb + ($ab - $bb) * $k)));
    }
}
imagecopyresampled($im, $small, 0, 0, 0, 0, W, H, $SW, $SH);
imagedestroy($small);

// Dot grid.
$dot = $c('#b1b8d6', 112);
for ($y = 14; $y < H; $y += 26) for ($x = 14; $x < W; $x += 26) imagefilledellipse($im, $x, $y, 3, 3, $dot);

// Accent rule along the top.
imagefilledrectangle($im, 0, 0, W, 5, $c('#6366f1'));

// Wordmark: "samirhv" then the accent dot, measured so they never overlap.
$mark = 'samirhv';
imagettftext($im, 92, 0, 96, 252, $c('#eef0f7'), $F_BOLD, $mark);
$box = imagettfbbox(92, 0, $F_BOLD, $mark);
imagettftext($im, 92, 0, 96 + ($box[2] - $box[0]) + 6, 252, $c('#6366f1'), $F_BOLD, '.');

imagettftext($im, 30, 0, 100, 320, $c('#a9b0c7'), $F_REG, 'Projects, builds and downloads');

// The four applications, as chips.
$x = 96;
foreach (['ShvIA', 'GitHub Desktop', 'ai-usagebar', 'SShvTerm'] as $app) {
    $b = imagettfbbox(19, 0, $F_MONO, $app);
    $w = ($b[2] - $b[0]) + 48;
    imagefilledrectangle($im, $x, 392, $x + $w, 444, $c('#12141d'));
    imagerectangle($im, $x, 392, $x + $w, 444, $c('#b1b8d6', 108));
    imagettftext($im, 19, 0, $x + 24, 425, $c('#8b93aa'), $F_MONO, $app);
    $x += $w + 18;
}

imagettftext($im, 18, 0, 98, 548, $c('#a5b4fc'), $F_MONO, 'samirhv.com.br');
imagettftext($im, 17, 0, 98, 583, $c('#8b93aa'), $F_REG, 'Every build counted, audited and traceable to its source.');

imagepng($im, $argv[1], 9);
echo "written: {$argv[1]}\n";
