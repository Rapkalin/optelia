const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const isDev = process.env.NODE_ENV === 'development';

module.exports = {
    entry: './assets/scripts/app.js',
    output: {
        filename: 'main.js',
        path: path.resolve(__dirname, 'dist'),
        clean: true,
    },
    mode: isDev ? 'development' : 'production',
    watch: isDev,
    devtool: isDev ? 'eval-cheap-module-source-map' : false,
    module: {
        rules: [
            {
                test: /\.s?css$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'postcss-loader',
                        options: { postcssOptions: { plugins: ['autoprefixer'] } },
                    },
                    'sass-loader',
                ],
            },
            {
                // Self-hosted typefaces (@fontsource): served from dist/, never from a CDN.
                test: /\.woff2?$/i,
                type: 'asset/resource',
                generator: { filename: 'fonts/[name][ext]' },
            },
        ],
    },
    plugins: [new MiniCssExtractPlugin({ filename: 'main.css' })],
};
