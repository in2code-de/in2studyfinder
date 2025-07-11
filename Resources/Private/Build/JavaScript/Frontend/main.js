import {In2studyfinder} from "./in2studyfinder/in2studyfinder";

if (document.querySelector('.in2studyfinder') !== null) {
  const in2studyfinder = new In2studyfinder();
  in2studyfinder.init();

  window.in2studyfinder = in2studyfinder;
}
