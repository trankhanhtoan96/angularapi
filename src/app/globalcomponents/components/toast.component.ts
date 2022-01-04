import {Component, OnInit, ViewChild} from '@angular/core';
import {ToastContainerDirective, ToastrService} from "ngx-toastr";

@Component({
    selector: 'ToastComponent',
    template: `
        <div toastContainer></div>
    `
})
export class ToastComponent implements OnInit {
    @ViewChild(ToastContainerDirective, {static: true})
    private toastContainer: ToastContainerDirective;

    constructor(private toastrService: ToastrService) {
    }

    ngOnInit(): void {
        this.toastrService.overlayContainer = this.toastContainer;
    }

    success(msg) {
        this.toastrService.success(msg)
    }

    error(msg) {
        this.toastrService.error(msg);
    }

    warning(msg) {
        this.toastrService.warning(msg);
    }

    info(msg) {
        this.toastrService.info(msg);
    }
}
