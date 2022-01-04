import {Component, OnInit} from '@angular/core';
import {Router} from "@angular/router";

@Component({
    selector: 'app-root',
    template: `
        <div class="wrapper">
            <ng-container *ngIf="showHeaderFooter">
                <AdsTopComponent></AdsTopComponent>
                <HeaderComponent></HeaderComponent>
            </ng-container>
            <router-outlet></router-outlet>
        </div>
    `
})
export class AppComponent implements OnInit {
    public showHeaderFooter: boolean = false;

    constructor(private router: Router) {
    }

    ngOnInit() {
        this.router.events.subscribe((val) => {
            this.showHeaderFooter = !/^\/login$/.test(this.router.url);
        });
    }
}
