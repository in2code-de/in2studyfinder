class LoaderUtility {

  static enableLoader() {
    document.querySelector(this.identifier.loader).classList.add(this.identifier.loaderActive.substring(1));
  };

  static disableLoader() {
    if (document.querySelector(this.identifier.loaderActive) !== null) {
      document.querySelector(this.identifier.loaderActive).classList.remove(this.identifier.loaderActive.substring(1));
    }
  };
}

LoaderUtility.identifier = {
  loader: '.in2studyfinder-loader',
  loaderActive: '.in2studyfinder-loader--active'
}

export {LoaderUtility}
