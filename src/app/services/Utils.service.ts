import {Injectable} from "@angular/core";

@Injectable()
export class Utils {

    constructor() {
    }

    protected getRand(): number {
        return Math.random();
    }

    private S4() {
        return (((1 + this.getRand()) * 0x10000) | 0).toString(16).substring(1);
    }

    generateGuid() {
        return (this.S4() + this.S4() + "-" + this.S4() + "-" + this.S4() + "-" + this.S4() + "-" + this.S4() + this.S4() + this.S4());
    }

}
