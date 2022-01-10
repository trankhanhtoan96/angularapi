import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";

@Component({
    selector: 'FooterComponent',
    templateUrl: '../templates/Footer.html'
})
export class FooterComponent implements OnInit {

    constructor(public session: Session) {
    }

    ngOnInit(): void {
    }
}
