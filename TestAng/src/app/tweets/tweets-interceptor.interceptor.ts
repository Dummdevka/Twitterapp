import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { TestServiceService } from '../tweets-service.service';
// import { HttpErrorResponse } from '@angular/common/http';
// import { Router } from '@angular/router';
@Injectable()
export class TweetsInterceptorInterceptor implements HttpInterceptor {

  constructor(public tweetsService: TestServiceService) {}

  intercept(httpRequest: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const token = localStorage.getItem('token');
    //this.checkAllow();
    return next.handle(httpRequest.clone({ setHeaders: { Authorization: `Bearer ${token}` }}));
    
  }

  // checkAllow(){
  //   this.tweetsService.refreshToken().subscribe(
  //     res => {
  //       if(res){
  //         //Storing refreshed token
  //           try{
  //             localStorage.setItem('token', res.jwt);
  //             console.log('refreshed');
  //           } catch(error){
  //             console.log(error);
  //           }
  //       }
  //       if(!res){
  //         //In case the token is valid          
  //         console.log('valid');
  //       }
  //     },
  //     err => {
  //       //If there are any errors - log out
  //       if(err instanceof HttpErrorResponse){
  //         if(err.status === 404){
  //           console.log(err.message);
  //         }
  //         if(err.status === 403){
  //           console.log('No refresh token');
  //         }
  //         this.router.navigate(['/tweets']);
  //       }
  //     })
  // }
}
