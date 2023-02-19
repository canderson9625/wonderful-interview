const path = require('path');

module.exports = {
  entry: './assets/ts/index.ts',
  module: {
    rules: [
      {
        test: /\.ts?$/,
        use: 'ts-loader',
        exclude: /node_modules/,
      },
    ],
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js'],
  },
  output: {
    filename: 'bundle.js',
    path: path.resolve(__dirname, 'assets/js'),
  },
  devServer: {
    static: path.join(__dirname, "assets/js"),
    compress: true,
    port: 4000,
  },
};
