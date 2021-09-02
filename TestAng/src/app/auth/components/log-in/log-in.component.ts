import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { AuthService } from '../../auth.service';
import { User } from 'src/app/User';
import { Token } from 'src/app/Token';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
@Component({
  selector: 'app-log-in',
  templateUrl: './log-in.component.html',
  styleUrls: ['./log-in.component.css']
})
export class LogInComponent implements OnInit {
  loginForm!: FormGroup;
  errors!:string;
  constructor(private fb: FormBuilder, private authServuce: AuthService, private router:Router) {
   }

  ngOnInit(): void {
    this.loginForm = this.fb.group({
      email: [ '',[
      Validators. required,
      Validators.email]
      ],
      pass: [ '',[
      Validators.required]
      ]
    })
  }
  get email(){
    return this.loginForm.get('email');
  }
  get pass(){
    return this.loginForm.get('pass');
  }
  onSubmitLogin(email:string, pass:string){
    if(this.loginForm.status==='VALID'){
        const loginData: User = {
        email: email,
        pass: pass
      }

      this.authServuce.logIn(loginData).subscribe(
        (res: Token)=>{
          console.log(res.message);
          localStorage.setItem('token', res.jwt);
        },
        err => { if(err instanceof HttpErrorResponse){
          if(err.status === 422){
            console.log(err.error);
          }
        }}
      );

      this.loginForm.reset();
    } else{
      alert("You can't submit an invalid form!");
    }
  }
}
