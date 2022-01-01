import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from "@angular/router";
import {Meta, Title} from "@angular/platform-browser";


@Component({
    selector: 'app-about',
    templateUrl: './about.component.html'
})
export class AboutComponent implements OnInit {
    constructor(private route: ActivatedRoute, private title: Title, private meta: Meta) {
        this.title.setTitle('About');
        this.meta.addTags([
            {name: 'keyword', content: 'abc'},
            {name: 'description', content: 'des'}
        ]);
    }

    ngOnInit(): void {
    }

}
