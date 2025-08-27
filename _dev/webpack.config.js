const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';

    return {
        entry: {
            style: path.resolve(__dirname, 'assets/scss/style.scss'),  // File principale SCSS
            script: path.resolve(__dirname, 'assets/js/main.js'),     // File principale JS
        },
        output: {
            path: path.resolve(__dirname, 'build'),
            filename: 'js/[name]-[contenthash].js',  // Aggiungi hash al nome del file JS
            publicPath: '/assets/', // Percorso pubblico degli asset
        },
        module: {
            rules: [
                // Regola per i file SCSS
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,  // Estrai il CSS in un file separato
                        'css-loader',                 // Carica il CSS
                        'sass-loader',                // Compila SCSS in CSS
                    ],
                },
                // Regola per i file JS
                {
                    test: /\.js$/,
                    exclude: /node_modules/,        // Escludi la cartella node_modules
                    use: {
                        loader: 'babel-loader',      // Usa Babel per la compilazione del codice JS
                        options: {
                            presets: [
                                [
                                    '@babel/preset-env',
                                    {
                                        modules: false,  // Mantieni i moduli ES6 per webpack
                                        targets: {
                                            browsers: ['> 1%', 'last 2 versions']
                                        }
                                    }
                                ]
                            ]
                        },
                    },
                },
            ],
        },
        plugins: [
            new CleanWebpackPlugin(),  // Pulisce la cartella build prima di ogni nuova build
            new MiniCssExtractPlugin({
                filename: isProduction ? 'css/[name]-[contenthash].css' : 'css/[name]-[contenthash].css',  // Aggiungi hash anche al CSS in sviluppo
            }),
        ],
        optimization: {
            minimize: isProduction,
            minimizer: [
                new CssMinimizerPlugin(),  // Minifica il CSS in produzione
                new TerserPlugin(),        // Minifica il JS in produzione
            ],
        },
        devtool: isProduction ? false : 'source-map',  // Abilita i source maps in modalità sviluppo
    };
};
