// Standalone parked concept. Requires sharp; run with NODE_PATH pointing to its node_modules.
const fs=require('node:fs');
const path=require('node:path');
const sharp=require('sharp');
const out=__dirname;
const cyan='#34B3EC',navy='#0B0F17',white='#F4F7FA';
const shape='M80 12H34C20 12 12 20 12 34V39C12 47 16 52 24 56L64 76H16V92H64C78 92 88 82 88 68V63C88 55 84 50 76 46L36 28H80Z';
const mark=color=>`<path d="${shape}" fill="${color}"/>`;
const svg=(w,h,body,label)=>`<svg xmlns="http://www.w3.org/2000/svg" width="${w}" height="${h}" viewBox="0 0 ${w} ${h}" role="img" aria-label="${label}">${body}</svg>`;
const text=(x,y,n,label,color=white,weight=400)=>`<text x="${x}" y="${y}" font-family="Arial, Helvetica, sans-serif" font-size="${n}" font-weight="${weight}" fill="${color}">${label}</text>`;
const lockup=color=>`<g transform="scale(.62)">${mark(cyan)}</g>`+text(80,47,43,'samirhv',color,700);
const icon=svg(512,512,`<rect width="512" height="512" rx="112" fill="${cyan}"/><g transform="translate(56 48) scale(4)">${mark(navy)}</g>`,'S essencial app icon concept');
// Compact silhouette, aligned to the pixel grid instead of shrinking the wordmark.
const favicon=svg(16,16,`<rect width="16" height="16" rx="3" fill="${navy}"/><path fill="${cyan}" d="M13 2H6C3 2 2 3 2 5V6L10 11H3V14H10C13 14 14 13 14 10V9L6 5H13Z"/>`,'S essencial favicon concept');
const files={'symbol.svg':svg(100,104,mark(cyan),'S essencial symbol'),'symbol-monochrome.svg':svg(100,104,mark('currentColor'),'S essencial monochrome symbol'),'logo-on-dark.svg':svg(252,66,lockup(white),'samirhv concept'),'logo-on-light.svg':svg(252,66,lockup(navy),'samirhv concept'),'app-icon.svg':icon,'favicon.svg':favicon};
(async()=>{
for(const [name,data]of Object.entries(files))fs.writeFileSync(path.join(out,name),data+'\n');
for(const n of [16,32,48,180])await sharp(Buffer.from(favicon)).resize(n,n).png().toFile(path.join(out,`favicon-${n}.png`));
for(const n of [192,512,1024])await sharp(Buffer.from(icon)).resize(n,n).png().toFile(path.join(out,`app-icon-${n}.png`));
for(const theme of ['dark','light'])await sharp(Buffer.from(files[`logo-on-${theme}.svg`])).resize(1260,330).png().toFile(path.join(out,`logo-on-${theme}.png`));
await sharp(Buffer.from(files['symbol.svg'])).resize(1000,1040).png().toFile(path.join(out,'symbol.png'));
let board=`<rect width="1440" height="1000" fill="${navy}"/>`;
board+=text(64,65,18,'03 / S ESSENCIAL',cyan,700)+text(64,116,33,'Geometria simples. Presença própria.')+text(64,151,17,'Refinamento reservado para samirhv · ainda não aplicado ao site','#A7B4C6');
board+=`<g transform="translate(75 213) scale(2.45)">${mark(cyan)}</g><g transform="translate(485 310) scale(2.8)">${lockup(white)}</g>`;
board+=`<line x1="64" y1="510" x2="1376" y2="510" stroke="#263445"/>`;
board+=text(64,558,17,'ASSINATURA EM FUNDO CLARO','#A7B4C6')+`<rect x="64" y="585" width="744" height="185" rx="12" fill="${white}"/><g transform="translate(108 630) scale(1.6)">${lockup(navy)}</g>`;
board+=text(870,558,17,'ÍCONE','#A7B4C6')+`<svg x="870" y="585" width="185" height="185" viewBox="0 0 512 512">${icon.replace(/^<svg[^>]*>|<\/svg>$/g,'')}</svg>`;
board+=text(1115,558,17,'FAVICON','#A7B4C6');
for(const[i,n]of [16,32,48].entries())board+=`<svg x="${1115+i*78}" y="625" width="${n}" height="${n}" viewBox="0 0 16 16">${favicon.replace(/^<svg[^>]*>|<\/svg>$/g,'')}</svg>`+text(1115+i*78,708,16,`${n} px`,'#A7B4C6');
board+=text(64,856,19,'Ciano #34B3EC   /   Azul profundo #0B0F17',cyan)+text(64,899,18,'Terminais retos · diagonal contínua · curvas externas discretas')+text(64,952,15,'Estudo de marca · SVG editável + PNG + ICO · sem alteração da identidade publicada','#A7B4C6');
const preview=svg(1440,1000,board,'S essencial refined concept for samirhv');fs.writeFileSync(path.join(out,'preview.svg'),preview+'\n');await sharp(Buffer.from(preview)).png().toFile(path.join(out,'preview.png'));
})();
