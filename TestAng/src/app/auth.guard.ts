import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { AuthService } from './auth/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard  {
  
  constructor (private authService: AuthService, private router: Router){

  }
  // canActivate(): boolean {
  //   if(this.authService.checkAllow() === 'auth'){
  //     //this.router.navigate(['/tweets']);
  //     return true;
  //   } else{
  //     this.router.navigate(['/login']);
  //     return false;
  //   }
  // }
}
