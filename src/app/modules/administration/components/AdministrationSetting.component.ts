import {Component, OnInit} from '@angular/core';
import {Session} from "../../../services/Session.service";
import {Model} from "../../../services/Model.service";
import {Metadata} from "../../../services/Metadata.service";
import {ToastComponent} from "../../../globalcomponents/components/toast.component";

@Component({
    selector: 'AdministrationSettingComponent',
    templateUrl: '../templates/AdministrationSetting.html'
})
export class AdministrationSettingComponent implements OnInit {
    config = [
        [
            {
                name: 'system_name',
                type: 'varchar',
                span: 4,
                label: 'Name'
            },
            {
                name: 'system_info',
                type: 'text',
                span: 8,
                label: 'Info'
            },
        ],
        [
            {
                name: 'system_favicon',
                type: 'image',
                span: 4,
                label: 'Favicon'
            },
            {
                name: 'system_image',
                type: 'image',
                span: 4,
                label: 'Logo'
            }
        ],
        [
            {
                name: 'ads_toptime',
                type: 'datetime',
                span: 4,
                label: 'Thời gian hết hạn Banner QC TOP'
            },
            {
                name: 'ads_topimage',
                type: 'image',
                span: 4,
                label: 'Banner'
            },
            {
                name: 'ads_topgg',
                type: 'text',
                span: 4,
                label: 'Google Ads Code'
            },
        ],
        [
            {
                name: 'ads_midtime',
                type: 'datetime',
                span: 4,
                label: 'Thời gian hết hạn Banner QC Mid'
            },
            {
                name: 'ads_midimage',
                type: 'image',
                span: 4,
                label: 'Banner'
            },
            {
                name: 'ads_midgg',
                type: 'text',
                span: 4,
                label: 'Google Ads Code'
            },
        ],
        [
            {
                name: 'ads_sidetime',
                type: 'datetime',
                span: 4,
                label: 'Thời gian hết hạn Banner QC Side Bar'
            },
            {
                name: 'ads_sideimage',
                type: 'image',
                span: 4,
                label: 'Banner'
            },
            {
                name: 'ads_sidegg',
                type: 'text',
                span: 4,
                label: 'Google Ads Code'
            },
        ],
        [
            {
                name: 'ads_auto',
                type: 'text',
                span: 12,
                label: 'Google Ads auto'
            },
        ]
    ];
    loadedSystemInfo: boolean = false;
    bean: any;

    constructor(
        public session: Session,
        public model: Model,
        public metadata: Metadata,
        public toast: ToastComponent
    ) {
    }

    ngOnInit(): void {
        this.model.$loadedSystemInfo.subscribe(res => {
            if (res) {
                // console.log('loaded system info');
                this.bean = this.session.setting;
                this.loadedSystemInfo = true;
            }
        })
    }

    saveData() {
        this.metadata.spinnerLoading().then(ref => {
            this.model.saveAdminSetting(this.bean).subscribe(res => {
                if (res.success) this.toast.success('Đã lưu thành công!');
                ref.instance.self.destroy();
            })
        })
    }

    getFieldRelateOptions(name) {
        return undefined;
    }
}
