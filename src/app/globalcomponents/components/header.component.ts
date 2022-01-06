import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";

@Component({
    selector: 'HeaderComponent',
    templateUrl: '../templates/Header.html'
})
export class HeaderComponent implements OnInit {

    constructor(public session: Session) {
    }

    ngOnInit(): void {

    }
}
