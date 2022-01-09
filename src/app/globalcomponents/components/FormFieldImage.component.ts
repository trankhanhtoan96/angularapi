import {Component, EventEmitter, Input, OnInit, Output, ViewChild} from '@angular/core';
import {environment} from "../../../environments/environment";

@Component({
    selector: 'FormFieldImageComponent',
    template: `
        <div class="text-center">
            <input accept="image/png,image/jpg,image/gif,image/jpeg" #inputFile (change)="imageUploadHandler()" type="file" [name]="name" class="form-control">
            <img *ngIf="src" [src]="src" alt="" class="mt-1" style="max-width: 250px;max-height: 200px">
        </div>
    `
})
export class FormFieldImageComponent implements OnInit {
    @Input() name: any;
    @Input() src: any;
    @Output() onData = new EventEmitter();
    @ViewChild('inputFile') inputFile;

    constructor() {
    }

    ngOnInit(): void {
    }

    getBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    imageUploadHandler() {
        this.getBase64(this.inputFile.nativeElement.files[0]).then(res => {
            if (res) {
                fetch(environment.apiUrl + '/common/bean/file/upload', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'apikey': environment.apiKey
                    },
                    // @ts-ignore
                    body: JSON.stringify({file: res.replace(/^data:image\/.+;base64,/, '')})
                }).then(res => res.json())
                    .then(res => {
                        this.src = environment.apiUrl + '/upload/' + res.file_md5;
                        this.onData.emit(this.src);
                    });
            }
        });
    };
}
