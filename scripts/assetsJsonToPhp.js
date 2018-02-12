const path = require('path');
const fs = require('fs');

const assetManifest = {
  json: path.join(process.cwd(), '/src/templates/assets.json'),
  php: path.join(process.cwd(), '/src/templates/assets.php'),
}

fs.readFile(assetManifest.json, 'utf-8', (err, jsonString) => {
  const json = JSON.parse(jsonString);

  let phpStr = '<?php \n$assets = [\n';
  for (const key in json) {
    phpStr += `    '${key.replace('.sass', '.css')}' => '${json[key]}',\n`
  }
  phpStr += '];'

  fs.writeFile(assetManifest.php, phpStr, 'utf-8', console.log);
});
