import {ApplicationRef, Component, OnDestroy, ViewChild, ViewContainerRef} from '@angular/core';

@Component({
    selector: 'BackdropComponent',
    template: `
        <div class="modal  fade show" tabindex="-1" style="display: block;backdrop-filter: brightness(0.6)" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body" style="padding:0.5rem;text-align: center">
                        <ng-container #target></ng-container>
                    </div>
                </div>
            </div>
        </div>
    `
})
export class BackdropComponent implements OnDestroy {
    public self: any = null;
    public zIndex: number;
    public childComponent: any;

    @ViewChild('target', {read: ViewContainerRef, static: true}) target: ViewContainerRef;

    constructor(private application: ApplicationRef) {
    }

    closeModal() {
        this.self.destroy();
    }

    ngOnDestroy() {
        this.application.tick();
    }
}
