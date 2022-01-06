import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Backend} from "../../services/Backend.service";
import {ToastComponent} from "../../globalcomponents/components/toast.component";

@Component({
    selector: 'HomeComponent',
    templateUrl: '../templates/home.html'
})
export class HomeComponent implements OnInit {

    constructor(
        private session: Session,
        private backend: Backend,
        private toast: ToastComponent
    ) {
    }

    ngOnInit(): void {
        this.backend.getRequestNoAuth('module/Contacts', {
            limit: 20,
            searchterm: '',
            offset: 0
        }).subscribe(res => console.log(res));
    }

    showAlert() {
        this.toast.success('okokok');
        this.toast.error('okokok');
        this.toast.warning('okokok');
        this.toast.info('okokok');
    }
}
