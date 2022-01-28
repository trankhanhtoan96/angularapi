import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Metadata} from "../../services/Metadata.service";
import {Backend} from "../../services/Backend.service";
import {ToastComponent} from "./toast.component";
import {Utils} from "../../services/Utils.service";

@Component({
    selector: 'FooterComponent',
    templateUrl: '../templates/Footer.html'
})
export class FooterComponent implements OnInit {
    emailsubcribe: string;

    constructor(public session: Session, private metadata: Metadata, private backend: Backend, private toast: ToastComponent, private utils: Utils) {
    }

    ngOnInit(): void {
    }

    subcribeEmail() {
        this.metadata.spinnerLoading().then(ref => {
            const id = this.utils.generateGuid();
            this.backend.postRequestNoAuth('module/BlogSubcribeEmail/' + id, {}, {
                id: id,
                name: this.emailsubcribe
            }).subscribe(res => {
                this.toast.success('Cảm ơn bạn đã đăng ký thành công!');
                ref.instance.self.destroy();
            });
        });
    }
}
