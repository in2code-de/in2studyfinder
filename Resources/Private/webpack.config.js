const path = require('path');

module.exports = {
  mode: 'development',
  entry: ['./JavaScript/Frontend/main.js','./Sass/backend.scss','./Sass/demo.scss','./Sass/style.scss'],
  output: {
    path: path.resolve(__dirname, '../Public/'),
    filename: 'JavaScript/[name].js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
        }
      },
      {
        test: /.scss$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '../Public/Css/[name].css',
            }
          },
          {
            loader: 'sass-loader'
          }
        ]
      }
    ]
  },
};
