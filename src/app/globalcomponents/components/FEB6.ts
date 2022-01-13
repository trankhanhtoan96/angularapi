import {Component} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Model} from "../../services/Model.service";
import {ToastComponent} from "./toast.component";
import {Metadata} from "../../services/Metadata.service";

@Component({
    selector: 'FEB6',
    template: `
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body" style="text-align: left">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập: {{bean.user_name}}</label>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Họ</label>
                                <input [(ngModel)]="bean.last_name" type="text" class="form-control">
                            </div>
                            <div class="col">
                                <label class="form-label">Tên</label>
                                <input [(ngModel)]="bean.first_name" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Email</label>
                                <input [(ngModel)]="bean.t_email" type="text" class="form-control">
                            </div>
                            <div class="col">
                                <label class="form-label">Ảnh đại diện</label>
                                <FormFieldImageComponent [src]="bean.user_image" (onData)="bean.user_image=$event"></FormFieldImageComponent>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <textarea [(ngModel)]="bean.description" type="text" class="form-control"></textarea>
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
        </div>
    `
})
export class FEB6 {
    self: any;
    bean: any;

    constructor(public session: Session, private model: Model, private toast: ToastComponent, private metadata: Metadata) {
        this.model.get('Users', this.session.authData.userid).subscribe(res => {
            this.bean = res;
        });
    }

    save() {
        this.metadata.spinnerLoading().then(ref => {
            this.model.save('Users', this.bean).subscribe(res => {
                ref.instance.self.destroy();
                this.toast.success('Đã lưu');
                this.self.destroy();
                this.bean = res;
            });
        });
    }
}
