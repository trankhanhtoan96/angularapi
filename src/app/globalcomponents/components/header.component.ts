import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Metadata} from "../../services/Metadata.service";
import {FEB7} from "./FEB7";

@Component({
    selector: 'HeaderComponent',
    templateUrl: '../templates/Header.html'
})
export class HeaderComponent implements OnInit {
    searchterm: string;

    constructor(public session: Session, private metadata: Metadata) {
    }

    ngOnInit(): void {
    }

    changePass() {
        this.metadata.modal(FEB7, 'large');
    }
}
