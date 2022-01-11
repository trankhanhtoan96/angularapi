import {Component, OnInit} from '@angular/core';
import {Backend} from "../../services/Backend.service";
import {ActivatedRoute, Router} from "@angular/router";

@Component({
    selector: 'FECategoryID',
    template: ``
})
export class FECategoryID implements OnInit {
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
            this.backend.getRequest('frontend/getcategoryslug/' + this.id).subscribe(res => {
                this.route.navigate(['/category/'+res.slug]);
            });
        });
    }
}
