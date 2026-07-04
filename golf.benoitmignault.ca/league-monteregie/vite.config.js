import { defineConfig } from 'vite'
// import { visualizer } from "rollup-plugin-visualizer";
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react(),  /* visualizer({open: true, gzipSize: true})] */ ],
  
  // Définir la base pour que les ressources soient correctement chargées 
  // même si l'application est servie à partir d'un sous-répertoire
  // Surtout que en PROD : https://golf.benoitmignault.ca/league-monteregie
  base: '/league-monteregie/'
})