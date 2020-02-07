const path = require('path');

module.exports = {
  mode: 'development',
  entry: './JavaScript/Frontend/main.js',
  output: {
    path: path.resolve(__dirname, '../Public/JavaScript/'),
    filename: 'main.js'
  },
  module: {
    rules: [{
      test: /\.js$/,
      exclude: /node_modules/,
      use: {
        loader: 'babel-loader',
      }
    }]
  },
};
