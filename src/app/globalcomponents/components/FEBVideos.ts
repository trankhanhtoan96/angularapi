import {Component} from '@angular/core';
import {DomSanitizer} from "@angular/platform-browser";

@Component({
    selector: 'FEBVideos',
    template: `
        
                    <div class="row">
                        <div class="col-md-8">
                            <div class="video-wrapper">
                                <iframe style="border:0" width="928" height="522" [src]="getTrueLink('https://www.youtube-nocookie.com/embed/'+currentVideo)" title="YouTube" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>
                            </div>
                            <br>
                            <h2 class="style-title">{{currentVideoName}}</h2>
                            <a target="_blank" href="https://www.youtube.com/channel/UCc7zbfRjxK20QZtSkascI7A/videos">
                                Xem tất cả videos SIMPLY TALK
                            </a>
                        </div>
                        <div class="col-md-4">
                            <div *ngFor="let video of videos" class="row row-0 align-items-center m-1">
                                <div class="col" (click)="currentVideoName=video.name;currentVideo=video.id">
                                    <table>
                                        <tr>
                                            <td class="cursor-pointer">
                                                <span class="avatar avatar-xl" style="background-image: url(https://i1.ytimg.com/vi/{{video.id}}/0.jpg)"></span>
                                            </td>
                                            <td class="p-1 cursor-pointer">
                                                <a class="style-titleArticle cursor-pointer">{{video.name}}</a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
    `
})
export class FEBVideos {
    currentVideo = 'ojsl-R0tCgI';
    currentVideoName = 'Chiến tranh tiền tệ là gì và nó ảnh hưởng đến bạn như thế nào?';
    videos = [
        {
            name: 'SIMPLY TALK 2: NGUY VÀ CƠ NỮA CUỐI 2021',
            id: 'jeCGvX9tKlo'
        },
        {
            name: 'Chiến tranh tiền tệ là gì và nó ảnh hưởng đến bạn như thế nào?',
            id: 'ojsl-R0tCgI'
        },
        {
            name: 'End Game vs Avatar, ai thắng? Sẽ ra sao nếu Avatar ra mắt năm 2019',
            id: '1WVTzOnLju4'
        },
        {
            name: 'Lãi suất là gì và ngân hàng kiếm tiền như thế nào?',
            id: 'wljNheIy3Cw'
        },
    ]

    constructor(private sanitizer: DomSanitizer) {
    }

    getTrueLink(src:string){
        return this.sanitizer.bypassSecurityTrustResourceUrl(src);
    }
}
