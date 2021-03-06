import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { HttpInterceptor } from '@angular/common/http';
import { TweetsInterceptorInterceptor } from './tweets/tweets-interceptor.interceptor';
import { HashLocationStrategy } from '@angular/common';
import { Tweet } from './Tweet';
import { Token } from './Token';
const httpOptions = {
  headers: new HttpHeaders({
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': '*',
    'Access-Control-Allow-Methods': '*'
  }),
  withCredentials: true
};
//const token = this.getToken();
//console.log(token);
const httpAuthHeader = {
  headers: new HttpHeaders
  ({
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': '*',
    'Access-Control-Allow-Methods': '*',
    //'Authorization': `Bearer ${token}`
}),
  withCredentials: true
};
const httpTweetHeader = {
  headers: new HttpHeaders
  ({
    //'Content-Type': ,
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': '*',
    'Access-Control-Allow-Methods': '*',
    //'Authorization': `Bearer ${token}`
}),
  withCredentials: true
}
@Injectable({
  providedIn: 'root'
})
export class TestServiceService {
  // public getToken(): string {
  //   return localStorage.getItem('token');
  // }  
  private apiUrl = 'http://localhost/twitterapp/Twitter';
  constructor( private http:HttpClient) { 
  }
  getTweets():Observable<Tweet[]>{
  const url = `${this.apiUrl}/?page=index`;
  return this.http.get<Tweet[]>(url,httpAuthHeader);
  }

  postTweet(tweet:FormData):Observable<Tweet[]>{
    const url = `${this.apiUrl}/?page=index&action=add`;
    return this.http.post<Tweet[]>(url,tweet, httpTweetHeader);
  }

  removeTweet(tweet:Tweet){
    const url = `${this.apiUrl}/?action=delete&id=${tweet.id}`;
    return this.http.get<Tweet[]>(url, httpAuthHeader);
  }
  //Refresh access token (when displaying tweets+checking if the user is allowed to see the page)
  async refreshToken(): Promise <Token>{
    const url = `${this.apiUrl}/?page=auth&action=refresh`;
    //console.log(token);
    
    return await this.http.get <Token> (url, httpAuthHeader).toPromise();
  }
  clearRefresh(): Observable <boolean>{
    const url = `${this.apiUrl}/?page=auth&action=clear`;
    return this.http.get <boolean> (url, httpAuthHeader);
  }
  sendImage(imageData: FormData): Observable <Object>{
    const url = `${this.apiUrl}/?page=index&action=saveImage`;
    return this.http.post <Object> (url, imageData, httpTweetHeader);

  }
}
