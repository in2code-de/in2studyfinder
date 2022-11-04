import {Instance} from "../components/instance";

class In2studyfinder {

  constructor() {
    this.identifier = {
      container: '.in2studyfinder',
    };
    this.instances = [];
  }

  init() {
    document.querySelectorAll(this.identifier.container).forEach(function(item, index) {
      item.classList.replace('no-js', 'js');
      item.setAttribute('data-in2studyfinder-instance-id', index);
      this.instances[index] = new Instance(item);
      this.instances[index].init();
    }.bind(this));
  }

  getInstance(instanceId) {
    if (this.instances[instanceId] !== undefined) {
      return this.instances[instanceId];
    }

    return null;
  }
}

export {In2studyfinder}
