import {Component, OnInit} from '@angular/core';
import {Title, Meta} from '@angular/platform-browser';

@Component({
    selector: 'HeaderComponent',
    templateUrl: '../templates/header.html'
})
export class HeaderComponent implements OnInit {

    constructor(private title: Title, private meta: Meta) {
        this.title.setTitle('Home');
        this.meta.addTags([
            {name: 'keyword', content: 'abc'},
            {name: 'description', content: 'des'}
        ]);
    }

    ngOnInit(): void {

    }
}
