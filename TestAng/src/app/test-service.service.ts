import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { HttpInterceptor } from '@angular/common/http';
import { HashLocationStrategy } from '@angular/common';
import { Tweet } from './Tweet';

const httpOptions = {
  headers: new HttpHeaders({
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': '*'
  })
};
@Injectable({
  providedIn: 'root'
})
export class TestServiceService {
  private apiUrl = 'http://localhost/learnang/Twitter';
  constructor( private http:HttpClient) { 

  }
  getTweets():Observable<Tweet[]>{
  const url = `${this.apiUrl}/?page=index`;
  return this.http.get<Tweet[]>(url);
  }

  postTweet(tweet:Tweet):Observable<Tweet[]>{
    const url = `${this.apiUrl}/?page=index&action=add`;
    return this.http.post<Tweet[]>(url,tweet, httpOptions);
  }

  removeTweet(tweet:Tweet){
    const url = `${this.apiUrl}/?action=delete&id=${tweet.id}`;
    return this.http.get<Tweet[]>(url);
  }
}
