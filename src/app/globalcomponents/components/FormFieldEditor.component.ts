import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {environment} from "../../../environments/environment";

@Component({
    selector: 'FormFieldEditorComponent',
    template: `
        <editor (onChange)="onData.emit(content)" [(ngModel)]="content" [init]="config"></editor>
    `
})
export class FormFieldEditorComponent implements OnInit {
    public config: any;
    @Input() content: any;
    @Output() onData = new EventEmitter();

    constructor() {
        this.config = {
            base_url: '/tinymce',
            suffix: '.min',
            height: 400,
            plugins: ['fullscreen link image code media table lists image powerpaste'],
            toolbar: 'fullscreen formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | image',
            images_upload_handler: this.imageUploadHandler,
            convert_urls: false
        }
    }

    ngOnInit(): void {
    }

    async imageUploadHandler(blobInfo, success, failure, progress) {
        progress(50);
        let response = await fetch(environment.apiUrl + '/common/bean/file/upload', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'apikey': environment.apiKey
            },
            body: JSON.stringify({file: blobInfo.base64()})
        });
        response = await response.json();
        progress(100);
        // @ts-ignore
        success(environment.apiUrl + '/upload/' + response.file_md5);
    };
}
