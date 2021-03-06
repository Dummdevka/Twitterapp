import { Injectable } from '@angular/core';
import { User } from '../User';
import { Token } from '../Token';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { HttpHeaders } from '@angular/common/http';

const httpOptions = {
  headers: new HttpHeaders({
    //'Access-Control-Allow-Origin': 'http://localhost:4200',
    'Content-Type': 'application/json',
    //'Access-Control-Allow-Headers': '*',
    //'Access-Control-Allow-Methods': '*'
  }),
  withCredentials: true
}
@Injectable({
  providedIn: 'root'
})
export class AuthService {
  apiUrl:string = "http://localhost/twitterapp/Twitter";
  constructor(private http: HttpClient) { }
  addUser(user:User): Observable <User>{
    const url = `${this.apiUrl}/?page=auth&action=signup`;
    return this.http.post <User>(url,user, httpOptions);
  }
  logIn(loginData:User): Observable <Token>{
    const url = `${this.apiUrl}/?page=auth&action=login`;
    return this.http.post <Token>(url, loginData, httpOptions);
  }
  refreshToken(): Observable <Token>{
    const url = `${this.apiUrl}/?page=auth&action=refresh`;
    return this.http.get <Token> (url, httpOptions);
  }
  checkAllow(){
    if(localStorage.getItem('token')){
      return true;
    } else {
      return false;
    }
  }
}
