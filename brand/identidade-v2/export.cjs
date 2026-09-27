// Export source SVGs. Requires sharp, resolved through NODE_PATH when not installed locally.
const fs=require('node:fs');const path=require('node:path');const sharp=require('sharp');
const root=__dirname,assets=path.join(root,'assets');
(async()=>{
for(const name of fs.readdirSync(assets).filter(n=>n.endsWith('.svg')&&!n.includes('mono'))){
  if(name.startsWith('favicon')||name.startsWith('app-icon')||name.startsWith('mobile-icon'))continue;
  await sharp(path.join(assets,name),{density:192}).png().toFile(path.join(assets,name.replace('.svg','.png')));
}
for(const theme of ['cyan','dark'])for(const n of [192,512,1024])await sharp(path.join(assets,`app-icon-${theme}.svg`)).resize(n,n).png().toFile(path.join(assets,`app-icon-${theme}-${n}.png`));
for(const n of [16,32,48])await sharp(path.join(assets,'favicon.svg')).resize(n,n).png().toFile(path.join(assets,`favicon-${n}.png`));
await sharp(path.join(assets,'mobile-icon-source.svg')).resize(1024,1024).flatten({background:'#0BB8EE'}).removeAlpha().png().toFile(path.join(assets,'mobile-icon-1024.png'));
await sharp(path.join(assets,'mobile-icon-source.svg')).resize(180,180).flatten({background:'#0BB8EE'}).removeAlpha().png().toFile(path.join(assets,'apple-touch-icon.png'));
// Social output dimensions follow platform canvas sizes, not the preview export density.
for(const name of ['social-cover','repository-cover'])await sharp(path.join(assets,`${name}.svg`)).png().toFile(path.join(assets,`${name}.png`));
console.log('PNG exports complete');
})().catch(e=>{console.error(e);process.exitCode=1});
