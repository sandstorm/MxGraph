const esbuild = require('esbuild');
const CssModulesPlugin = require('esbuild-css-modules-plugin');
const extensibilityMap = require('@neos-project/neos-ui-extensibility/extensibilityMap.json');
const isWatch = process.argv.includes('--watch');

/** @type {import("esbuild").BuildOptions} */
const options = {
    logLevel: 'info',
    bundle: true,
    target: 'es2020',
    entryPoints: {Plugin: 'src/index.js'},
    loader: {
        '.js': 'tsx',
        '.css': 'css'
    },
    outdir: '../../../Public/MxGraph',
    alias: extensibilityMap,
    plugins: [
        CssModulesPlugin({
            force: true,
            emitDeclarationFile: true,
            localsConvention: 'camelCaseOnly',
            namedExports: true,
            inject: false
        })
    ]
};

if (isWatch) {
    esbuild.context(options).then((ctx) => ctx.watch());
} else {
    esbuild.build(options);
}
