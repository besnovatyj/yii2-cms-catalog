/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/**
 * ESBuild configuration for Catalog Showcase assets
 *
 * JS: src/js/showcase.ts → dist/js/showcase.js (Drag-and-drop, AJAX управление элементами витрины)
 */

import * as esbuild from 'esbuild';
import * as path from 'path';
import {fileURLToPath} from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const isWatch = process.argv.includes('--watch');

const buildJS = async () => {
    const options = {
        entryPoints: ['src/js/showcase.ts'],
        outfile: 'dist/js/showcase.js',
        bundle: true,
        format: 'iife',
        target: 'es2020',
        sourcemap: true,
        minify: true,
        treeShaking: true,
        platform: 'browser',
        tsconfig: './tsconfig.json',
        logLevel: 'info',
    };

    if (isWatch) {
        const ctx = await esbuild.context(options);
        await ctx.watch();
        console.log('Watching JS...');
    } else {
        await esbuild.build(options);
        console.log('JS build completed: dist/js/showcase.js');
    }
};

const build = async () => {
    try {
        console.log('Starting build...');
        await buildJS();

        if (isWatch) {
            console.log('Watch mode active. Waiting for changes...');
        } else {
            console.log('Build completed successfully!');
        }
    } catch (error) {
        console.error('Build failed:', error);
        process.exit(1);
    }
};

build();
