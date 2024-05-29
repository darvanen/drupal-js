import { defineConfig } from 'vite';
import { globSync } from 'glob';
import jsYaml from 'js-yaml';
const fs = require('fs');

// Function to extract JS files from a given YAML object
function extractJsFiles(data) {
  let jsFiles = [];

  function traverse(obj) {
    if (obj && typeof obj === 'object') {
      Object.keys(obj).forEach(key => {
        if (key === 'js' && typeof obj[key] === 'object') {
          jsFiles.push(...Object.keys(obj[key]));
        } else {
          traverse(obj[key]);
        }
      });
    }
  }

  traverse(data);
  return jsFiles;
}

export default defineConfig(({ mode }) => {
  const env = mode === 'production' ? '"production"' : '"development"';

  // Use glob to find all relevant YAML files
  const files = globSync('./web/modules/**/*.foxy.yml');

  // Use reduce to process each file and collect all JS files
  const inputs = files.reduce((acc, libraryFilePath) => {
    try {
      const moduleDirectoryPath = libraryFilePath.substring(0, libraryFilePath.lastIndexOf('/'));
      const fileContents = fs.readFileSync(libraryFilePath, 'utf8');
      const data = jsYaml.load(fileContents);
      const jsFiles = extractJsFiles(data);
      const jsFilesFull = jsFiles.map(x => moduleDirectoryPath + '/' + x);
      return acc.concat(jsFilesFull);
    } catch (err) {
      console.error(`Error processing file ${libraryFilePath}:`, err);
      return acc;
    }
  }, []);

  return {
    build: {
      baseUrl: 'web/libraries/compiled',
      manifest: true,
      cssCodeSplit: true,
      lib: {
        entry: inputs,
        name: 'drupal-libraries',
        fileName: '[name]',
      },
      outDir: 'web/libraries/compiled',
      rollupOptions: {
        input: inputs,
        external: '[Drupal, once]',
      },
    },
    css: { devSourcemap: true },
    define: { 'process.env.NODE_ENV': env },
  };
});
