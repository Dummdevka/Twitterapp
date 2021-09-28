import { Injectable } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { HttpHeaders } from '@angular/common/http';
import { User } from 'src/app/User';

const httpOptions = {
  headers: new HttpHeaders({
    'Content-Type': 'application/json'
  }),
  withCredentials: true
}
@Injectable({
  providedIn: 'root'
})
export class AccountService {
  apiUrl:string = "http://localhost/twitterapp/Twitter";
  constructor(private http: HttpClient) { }

  getData(): Observable <User> {
    const url = `${this.apiUrl}/?page=account`;
    return this.http.get <User> (url, httpOptions);
  }
  changeUsername(username: object): Observable <User>{
    const url = `${this.apiUrl}/?page=account&action=changeUsername`;
    return this.http.post <User> (url, username, httpOptions );

  }
}
