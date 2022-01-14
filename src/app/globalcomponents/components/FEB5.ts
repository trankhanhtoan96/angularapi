import {Component, Input, OnInit} from '@angular/core';
import {Backend} from "../../services/Backend.service";
import {ActivatedRoute} from "@angular/router";
import {Metadata} from "../../services/Metadata.service";
import {Session} from "../../services/Session.service";
import {ToastComponent} from "./toast.component";

@Component({
    selector: 'FEB5',
    template: `
        <div class="row mb-3">
            <div *ngIf="session.authData.id;else commentmustlogin" class="col">
                <textarea [(ngModel)]="content" maxlength="2000" class="form-control" rows="7" placeholder="Nội dung..."></textarea>
                <button (click)="sendComment()" class="btn btn-primary mt-2">Gửi</button>
            </div>
            <ng-template #commentmustlogin>
                <p>Bạn phải <a style="font-weight: bold;color:#0d0f39" routerLink="/login">đăng nhập</a> để gửi nội dung thảo luận.</p>
            </ng-template>
        </div>
        <div *ngFor="let comment of comments" class="card mb-3">
            <div class="row row-0">
                <div class="col">
                    <div class="card-body">
                        <div class="row">
                            <div class="col align-middle">
                                <span class="badge bg-yellow" style="color:#0d0f39">
                                    <span class="avatar" [style]="{'background-image': 'url('+session.setting.system_favicon+')'}"></span>
                                    {{comment.created_by_name}}
                                </span><br/>
                                <small><i class="ti ti-clock"></i> {{comment.date_entered|date:'dd/MM/yyyy'}}</small>
                            </div>
                        </div>
                        <p>{{comment.description}}</p>
                    </div>
                </div>
                <div class="col-auto">
                    <i (click)="reactComment(comment)" style="font-size:20px;color:#{{typeReact(comment.id)=='heart'?'000':'f59f00'}}" class="ti ti-{{typeReact(comment.id)}} {{typeReact(comment.id)=='heart' && session.authData.userid?'cursor-pointer':''}}">{{comment.reaction ? comment.reaction : 0}}</i>
                </div>
            </div>
        </div>
    `
})
export class FEB5 implements OnInit {
    @Input() blog: { id: string; };
    comments:any;
    authorReact:any;
    content: any;

    constructor(
        private backend: Backend,
        private router: ActivatedRoute,
        private metadata: Metadata,
        public session: Session,
        private toast: ToastComponent
    ) {
    }

    ngOnInit() {
        if (typeof sessionStorage != "undefined") {
            this.router.params.subscribe(params => {
                this.backend.getRequestNoAuth('frontend/blogcomment/' + params['slug'], {userid: this.session.authData.userid}).subscribe(res => {
                    // console.log(res);
                    this.comments = res.comments;
                    this.authorReact = res.authorReact;
                });
            });
        }
    }

    sendComment() {
        this.metadata.spinnerLoading().then(ref => {
            this.backend.postRequest('frontend/blogcomment/' + this.blog.id, {}, {content: this.content}).subscribe(res => {
                ref.instance.self.destroy();
            });
        });
    }

    typeReact(comment_id: string): 'heartbeat' | 'heart' {
        // @ts-ignore
        if (this.authorReact.indexOf(comment_id) >= 0) return 'heartbeat';
        return 'heart';
    }

    reactComment(comment: { id: string; }) {
        if (this.typeReact(comment.id) == 'heart' && this.session.authData.userid) {
            this.metadata.spinnerLoading().then(ref => {
                this.backend.postRequest('frontend/blogcommentreact/' + comment.id, {}, {userid: this.session.authData.userid}).subscribe(res => {
                    // @ts-ignore
                    this.authorReact.push(comment.id);
                    ref.instance.self.destroy();
                });
            });
        }
    }
}
