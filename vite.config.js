import { defineConfig } from 'vite'
import { globSync } from 'glob'
import jsYaml from 'js-yaml'
import { readFileSync } from 'fs'

export default defineConfig(({ mode }) => {
  const env = mode === 'production' ? '"production"' : '"development"'

  const inputs = getInputs()

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
  }
})

/**
 * Gets an array of input file paths.
 */
function getInputs() {
  // Use glob to find all relevant YAML files
  const files = globSync('./web/modules/**/*.foxy.yml')

  // Use reduce to process each file and collect all JS files
  return files.reduce((acc, libraryFilePath) => {
    try {
      const moduleDirectoryPath = libraryFilePath.substring(
        0,
        libraryFilePath.lastIndexOf('/'),
      )
      const moduleName = moduleDirectoryPath.substring(
        moduleDirectoryPath.lastIndexOf('/') + 1,
      )
      const fileContents = readFileSync(libraryFilePath, 'utf8')
      const data = jsYaml.load(fileContents)
      const jsFiles = extractJsFiles(data)
      jsFiles.forEach((filePath) => {
        const fileName = filePath.substring(
          filePath.lastIndexOf('/') + 1,
          filePath.lastIndexOf('.'),
        )
        const key = moduleName + '/' + fileName
        acc[key] = moduleDirectoryPath + '/' + filePath
      })
      return acc
    } catch (err) {
      console.error(`Error processing file ${libraryFilePath}:`, err)
      return acc
    }
  }, {})
}

/**
 * Extracts JS files from a given YAML object.
 */
function extractJsFiles(data) {
  let jsFiles = []

  function traverse(obj) {
    if (obj && typeof obj === 'object') {
      Object.keys(obj).forEach((key) => {
        if (key === 'js' && typeof obj[key] === 'object') {
          jsFiles.push(...Object.keys(obj[key]))
        } else {
          traverse(obj[key])
        }
      })
    }
  }

  traverse(data)
  return jsFiles
}
