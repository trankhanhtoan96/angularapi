import {Component} from '@angular/core';
import {ToastComponent} from "./toast.component";
import {Metadata} from "../../services/Metadata.service";
import {Backend} from "../../services/Backend.service";
import {Session} from "../../services/Session.service";
import {Router} from "@angular/router";

@Component({
    selector: 'FEB7',
    template: `
        <div class="modal-content">
            <div class="modal-body" style="text-align: left">
                <div class="row">
                    <div class="col">
                        <label class="form-label">Mật khẩu mới</label>
                        <input [(ngModel)]="p1" type="password" class="form-control">
                    </div>
                    <div class="col">
                        <label class="form-label">Lặp lại mật khẩu</label>
                        <input [(ngModel)]="p2" type="password" class="form-control">
                    </div>
                    <span *ngIf="p1 && p2 && p1!=p2" style="color: red">Mật khẩu không khớp</span>
                </div>
            </div>
            <div class="modal-footer">
                <a (click)="self.destroy()" href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                    hủy
                </a>
                <a (click)="save()" href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
                    Lưu
                </a>
            </div>
        </div>
    `
})
export class FEB7 {
    self: any;
    p1: string;
    p2: string;

    constructor(private router: Router,private toast: ToastComponent, private metadata: Metadata, private backend: Backend, private session: Session) {
    }


    save() {
        if (this.p1 && this.p2 && this.p1 == this.p2) {
            this.metadata.spinnerLoading().then(ref => {
                this.backend.postRequest('frontend/changepass', {}, {p: this.p1, u: this.session.authData.userid}).subscribe(res => {
                    ref.instance.self.destroy();
                    this.toast.success('Đã thay đổi mật khẩu');
                    this.self.destroy();
                    this.session.endSession();
                    this.router.navigate(['/login']);
                });
            });
        }
    }
}
