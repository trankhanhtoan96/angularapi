import {Injectable} from '@angular/core';
import {Backend} from "./Backend.service";
import {BehaviorSubject, Observable, Subject} from "rxjs";
import {Session} from "./Session.service";
import {Utils} from "./Utils.service";
import {environment} from "../../environments/environment";

@Injectable()
export class Model {
    $loadedSystemInfo = new BehaviorSubject(false);

    constructor(
        private backend: Backend,
        private session: Session,
        private utils: Utils
    ) {
        this.loadSystemInfo();
    }

    loadSystemInfo() {
        try {
            // @ts-ignore
            this.session.setting = JSON.parse(decodeURIComponent(window.atob(localStorage.getItem(environment.apiKey + 'setting'))));
            // @ts-ignore
            this.session.fields = JSON.parse(decodeURIComponent(window.atob(localStorage.getItem(environment.apiKey + 'fields'))));
            // @ts-ignore
            this.session.lang = JSON.parse(decodeURIComponent(window.atob(localStorage.getItem(environment.apiKey + 'lang'))));
            // @ts-ignore
            this.session.enum = JSON.parse(decodeURIComponent(window.atob(localStorage.getItem(environment.apiKey + 'enum'))));
            if (!this.session.lang) {
                this.backend.getRequestNoAuth('systeminfo').subscribe(res => {
                    this.session.fields = res.fields;
                    this.session.lang = res.lang;
                    this.session.enum = res.enum;
                    this.session.setting = res.setting;
                    localStorage.setItem(environment.apiKey + 'setting', window.btoa(encodeURIComponent(JSON.stringify(this.session.setting))));
                    localStorage.setItem(environment.apiKey + 'fields', window.btoa(encodeURIComponent(JSON.stringify(this.session.fields))));
                    localStorage.setItem(environment.apiKey + 'lang', window.btoa(encodeURIComponent(JSON.stringify(this.session.lang))));
                    localStorage.setItem(environment.apiKey + 'enum', window.btoa(encodeURIComponent(JSON.stringify(this.session.enum))));
                    this.$loadedSystemInfo.next(true);
                });
            } else {
                this.$loadedSystemInfo.next(true);
            }
        } catch (e) {
            if (!this.session.lang) {
                this.backend.getRequestNoAuth('systeminfo').subscribe(res => {
                    this.session.fields = res.fields;
                    this.session.lang = res.lang;
                    this.session.enum = res.enum;
                    this.session.setting = res.setting;
                    this.$loadedSystemInfo.next(true);
                });
            }
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

    list(module: string, limit: number, offset: number): Observable<any> {
        let subject = new Subject();
        this.backend.getRequest('module/' + module, {limit: limit, offset: offset, sortfields: [{"sortfield": "date_entered", "sortdirection": "DESC"}]})
            .subscribe(res => {
                subject.next(res);
                subject.complete();
            });
        return subject.asObservable();
    }

    search(module: string, searchterm: string, limit: number, offset: number): Observable<any> {
        let subject = new Subject();
        this.backend.getRequest('module/' + module, {limit: limit, offset: offset, searchterm: searchterm, sortfields: encodeURI('[{"sortfield":"date_entered","sortdirection":"DESC"}')})
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

    deletes(beanName: string, ids: string[]): Observable<any> {
        let subject = new Subject();
        this.backend.postRequest('deletemultirecord', {}, {ids: ids, bean: beanName}).subscribe(res => {
            subject.next(res);
            subject.complete();
        });
        return subject.asObservable();
    }

    saveAdminSetting(beanData: any): Observable<any> {
        let subject = new Subject();
        this.backend.postRequest('admin/saveadminsetting', {}, {data: beanData}).subscribe(res => {
            subject.next(res);
            subject.complete();
        });
        return subject.asObservable();
    }
}
