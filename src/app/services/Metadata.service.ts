import {ComponentFactoryResolver, Injectable, Injector} from '@angular/core';
import {FooterService} from "./Footer.service";
import {SpinnerCircularFixedComponent} from "spinners-angular/spinner-circular-fixed";
import {BackdropSmallComponent} from "../globalcomponents/components/BackdropSmall.component";
import {BackdropLargeComponent} from "../globalcomponents/components/BackdropLarge.component";

@Injectable()
export class Metadata {
    constructor(
        private componentFactoryResolver: ComponentFactoryResolver,
        private footer: FooterService
    ) {
    }

    loadComponent(conponent: any, viewChid: any, injector?: Injector): Promise<any> {
        return new Promise<any>(resolve => {
            let factory = this.componentFactoryResolver.resolveComponentFactory(conponent);
            let componentRef = viewChid.createComponent(factory, undefined, injector);
            componentRef.instance.self = componentRef;
            resolve(componentRef);
        });
    }

    spinnerLoading(): Promise<any> {
        return new Promise<any>((resolve => {
            this.loadComponent(BackdropSmallComponent, this.footer.footercontainer)
                .then(wrapperComponent => {
                    this.loadComponent(SpinnerCircularFixedComponent, wrapperComponent.instance.target)
                        .then(component => {
                            component.instance.self = wrapperComponent;
                            wrapperComponent.instance.childComponent = component;
                            resolve(component);
                        });
                });
        }));
    }

    modal(component: any, size: 'small' | 'large' = 'large'): Promise<any> {
        return new Promise<any>((resolve => {
            this.loadComponent(size == 'small' ? BackdropSmallComponent : BackdropLargeComponent, this.footer.footercontainer)
                .then(wrapperComponent => {
                    this.loadComponent(component, wrapperComponent.instance.target)
                        .then(component => {
                            component.instance.self = wrapperComponent;
                            wrapperComponent.instance.childComponent = component;
                            resolve(component);
                        });
                });
        }));
    }
}
