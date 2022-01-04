import {Injectable, EventEmitter} from '@angular/core';

interface broadcastMessage {
    type: string;
    data: any;
}

@Injectable()
export class Broadcast {
    public message$: EventEmitter<broadcastMessage> = new EventEmitter<broadcastMessage>();

    public broadcastMessage(message: string, data: any = {}): void {
        this.message$.emit({
            type: message,
            data: data
        });
    }
}
