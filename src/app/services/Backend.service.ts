import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders, HttpParams} from "@angular/common/http";
import {Subject, Observable} from 'rxjs';
import {Router} from '@angular/router';
import {Session} from "./Session.service";
import {environment} from "../../environments/environment";

@Injectable()
export class Backend {
    constructor(
        private http: HttpClient,
        private session: Session,
        private router: Router
    ) {
    }

    private getHeaders(): HttpHeaders {
        let headers = this.session.getSessionHeader();
        headers = headers.set('Accept', 'application/json');
        return headers;
    }


    private prepareParams(params: object): HttpParams {
        let output = new HttpParams();
        if (params) {
            Object.keys(params).forEach((key: string) => {
                let value = params[key];
                if (typeof value !== 'undefined' && value !== null) {
                    if (typeof value === 'object') {
                        output = output.append(key, JSON.stringify(value));
                    } else if (typeof value === 'boolean') {
                        output = output.append(key, value ? '1' : '0');
                    } else if (typeof value === 'number') {
                        output = output.append(key, value + '');
                    } else {
                        output = output.append(key, value.toString());
                    }
                }
            });
        }
        return output;
    }

    public getRequest(route: string = "", params: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();
        this.http.get(environment.apiUrl + "/" + encodeURI(route),
            {headers: this.getHeaders(), observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: err => {
                this.handleError(err, route, 'GET', {getParams: params});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    auth(authType: 'basic' | 'form', route: string, params: { username, password }): Observable<any> {
        let subject = new Subject<any>();
        let theaders = new HttpHeaders();
        theaders = theaders.set('Accept', 'application/json');
        theaders = theaders.set('Authorization', 'Basic ' + window.btoa(params.username + ':' + params.password));
        this.http.get(environment.apiUrl + "/" + encodeURI(route), {headers: theaders, observe: "response"}).subscribe({
            next: (res) => {
                subject.next(res.body);
                subject.complete();
            },
            error: err => {
                this.handleError(err, route, 'GET', {getParams: params});
                subject.error(err);
            }
        });
        return subject.asObservable();
    }

    public postRequest(route: string = "", params: any = {}, body: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();

        let headers = this.getHeaders();
        if (body) {
            headers = headers.set("Content-Type", "application/json");
        } else {
            headers = headers.set("Content-Type", "application/x-www-form-urlencoded");
        }

        this.http.post(environment.apiUrl + "/" + encodeURI(route), body,
            {headers: headers, observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: err => {
                this.handleError(err, route, 'POST', {getParams: params, body: body});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    public putRequest(route: string = "", params: any = {}, body: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();

        this.http.put(
            environment.apiUrl + "/" + route,
            body,
            {headers: this.getHeaders(), observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: (err) => {
                this.handleError(err, route, 'PUT', {getParams: params, body: body});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    public deleteRequest(route: string = "", params: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();

        this.http.delete(
            environment.apiUrl + "/" + route,
            {headers: this.getHeaders(), params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res ? res : true);
                responseSubject.complete();
            },
            error: (err) => {
                this.handleError(err, route, 'DELETE', {getParams: params});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    private handleError(err, route, method: string, data = null) {
        switch (err.status) {
            case 401:
                this.session.endSession();
                this.router.navigate(["/login"]);
                break;
            case 0:
                console.log(err, route, method, data);
                break;
        }
    }
}
