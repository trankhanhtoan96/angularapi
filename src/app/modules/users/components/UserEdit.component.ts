import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from "@angular/router";

@Component({
    selector: 'UserEditComponent',
    templateUrl: '../templates/edit.html'
})
export class UserEditComponent implements OnInit {
    id: string;

    constructor(
        private router: ActivatedRoute
    ) {
        this.router.params.subscribe(params => {
            this.id = params.id;
        });
    }

    ngOnInit(): void {
    }
}
