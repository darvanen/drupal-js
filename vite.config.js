import { defineConfig } from 'vite'
import { resolve } from 'path'
import { watchAndRun } from 'vite-plugin-watch-and-run'
import { globSync } from 'glob'


export default defineConfig(({ mode }) => {
 const env = mode === 'production' ? '"production"' : '"development"'

 const inputs = globSync('./web/modules/**/entry.js').reduce((acc, input) => {
   const library = input.match(/\/([^\/]+)\/entry\.js$/)[1]
   acc[library] = resolve(__dirname, input)
   return acc
 }, {})


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
   plugins: [
     watchAndRun([
       {
         name: 'twig-reload',
         watchKind: ['add', 'unlink'],
         watch: resolve('./templates/**/*.twig'),
         run: 'drush cr',
         delay: 300,
       },
     ]),
   ],
 }
})
