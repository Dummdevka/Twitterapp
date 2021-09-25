import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { TestServiceService } from '../tweets-service.service';

@Injectable()
export class TweetsInterceptorInterceptor implements HttpInterceptor {

  constructor(public tweetsService: TestServiceService) {}

  intercept(httpRequest: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const token = localStorage.getItem('token');
    
    return next.handle(httpRequest.clone({ setHeaders: { Authorization: `Bearer ${token}` }}));
    
  }
}
