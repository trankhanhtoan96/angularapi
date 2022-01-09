import {Component, Input, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import {Model} from "../../services/Model.service";
import {ToastComponent} from "../../globalcomponents/components/toast.component";
import {Metadata} from "../../services/Metadata.service";

@Component({
    selector: 'EditViewComponent',
    templateUrl: '../templates/EditView.html'
})
export class EditViewComponent implements OnInit {
    @Input() beanName: string = '';
    @Input() moduleName: string = '';
    @Input() bean: any;
    @Input() config = [];
    loadedSystemInfo: boolean = false;

    constructor(
        private router: ActivatedRoute,
        private model: Model,
        private toast: ToastComponent,
        private route: Router,
        private metadata: Metadata
    ) {

    }

    ngOnInit(): void {
        this.model.$loadedSystemInfo.subscribe(res => {
            if (res) {
                this.loadedSystemInfo = true;
                if (this.bean.id != 'create' && this.bean.id) {
                    this.metadata.spinnerLoading().then(ref => {
                        this.model.get(this.moduleName, this.bean.id).subscribe(res => {
                            this.bean = res;
                            ref.instance.self.destroy();
                        });
                    });
                }
            }
        });
    }

    get routerModule(): string {
        return String(this.moduleName).toLowerCase();
    }

    saveData() {
        this.metadata.spinnerLoading().then(ref => {
            this.model.save(this.moduleName, this.bean).subscribe(res => {
                this.toast.success('Đã lưu thành công!');
                ref.instance.self.destroy();
                this.route.navigate(['/admin/' + this.routerModule]);
            });
        });
    }
}
