import {Injectable} from '@angular/core';
import {Backend} from "./Backend.service";
import {BehaviorSubject, Observable, Subject} from "rxjs";
import {Session} from "./Session.service";
import {Utils} from "./Utils.service";

@Injectable()
export class Model {
    $loadedSystemInfo = new BehaviorSubject(false);

    constructor(
        private backend: Backend,
        private session: Session,
        private utils: Utils
    ) {
        if (!this.session.fields) {
            this.backend.getRequest('systeminfo').subscribe(res => {
                this.session.fields = res.fields;
                this.session.lang = res.lang;
                this.session.enum = res.enum;
                console.log(res);

                this.$loadedSystemInfo.next(true);
            });
        }
    }

    get(module: string, id: string): Observable<any> {
        let subject = new Subject();
        this.backend.getRequest('module/' + module + '/' + id)
            .subscribe(res => {
                subject.next(res);
                subject.complete();
            });
        return subject.asObservable();
    }

    save(beanName: string, bean: any): Observable<any> {
        let subject = new Subject();
        if (!bean.id || bean.id == 'create') bean.id = this.utils.generateGuid();
        this.backend.postRequest('module/' + beanName + '/' + bean.id, {}, bean).subscribe(res => {
            subject.next(res);
            subject.complete();
        });
        return subject.asObservable();
    }
}
