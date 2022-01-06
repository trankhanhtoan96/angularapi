import {AfterViewInit, Component, OnInit, ViewChild, ViewContainerRef} from '@angular/core';
import {Router} from "@angular/router";
import {FooterService} from "./services/Footer.service";

@Component({
    selector: 'app-root',
    template: `
        <div class="wrapper">
            <ng-container *ngIf="showHeaderFooter">
                <AdsTopComponent *ngIf="!showAdminMenu"></AdsTopComponent>
                <HeaderComponent *ngIf="!showAdminMenu"></HeaderComponent>
                <AdminMenuComponent *ngIf="showAdminMenu"></AdminMenuComponent>
            </ng-container>
            <router-outlet></router-outlet>
            <FooterComponent *ngIf="showHeaderFooter && !showAdminMenu"></FooterComponent>
        </div>
        <ToastComponent></ToastComponent>
        <ng-container #footercontainer></ng-container>
    `
})
export class AppComponent implements OnInit, AfterViewInit {
    public showHeaderFooter: boolean = false;
    public showAdminMenu: boolean = false;
    @ViewChild('footercontainer', {read: ViewContainerRef, static: true}) private footercontainer: ViewContainerRef;

    constructor(private router: Router, public footer: FooterService) {
    }

    public ngAfterViewInit() {
        this.footer.footercontainer = this.footercontainer;
    }

    ngOnInit() {
        this.router.events.subscribe(val => {
            this.showHeaderFooter = !/^\/login$/.test(this.router.url);
            this.showAdminMenu = /^\/admin/.test(this.router.url);
        });
    }
}
