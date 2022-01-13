import {Component, OnInit} from '@angular/core';
import {Backend} from "../../services/Backend.service";
import {ActivatedRoute, Router} from "@angular/router";

@Component({
    selector: 'FEBlogID',
    template: ``
})
export class FEBlogID implements OnInit {
    public id: string;

    constructor(
        public backend: Backend,
        private router: ActivatedRoute,
        private route:Router
    ) {
    }

    ngOnInit() {
        this.router.params.subscribe(params => {
            this.id = params.id;
            this.backend.getRequestNoAuth('frontend/getblogslug/' + this.id).subscribe(res => {
                this.route.navigate(['/blog/'+res.slug]);
            });
        });
    }
}
