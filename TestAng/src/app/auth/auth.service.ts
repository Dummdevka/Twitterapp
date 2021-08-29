import { Injectable } from '@angular/core';
import { User } from '../User';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { HttpHeaders } from '@angular/common/http';

const httpOptions = {
  headers: new HttpHeaders({
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': '*',
    'Access-Control-Allow-Methods': '*'
  })
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  apiUrl:string = "http://localhost/twitterapp/Twitter";
  
  constructor(private http: HttpClient) { }

  addUser(user:User): Observable <string []>{
    const url = `${this.apiUrl}/?page=auth&action=signup`;
    return this.http.post <string[]>(url,user, httpOptions);
  }
  logIn(loginData:User): Observable <User[]>{
    const url = `${this.apiUrl}/?page=auth&action=login`;
    return this.http.post <User[]>(url, loginData, httpOptions);
    
  }
}
