import { TestBed } from '@angular/core/testing';

import { TweetsInterceptorInterceptor } from './tweets-interceptor.interceptor';

describe('TweetsInterceptorInterceptor', () => {
  beforeEach(() => TestBed.configureTestingModule({
    providers: [
      TweetsInterceptorInterceptor
      ]
  }));

  it('should be created', () => {
    const interceptor: TweetsInterceptorInterceptor = TestBed.inject(TweetsInterceptorInterceptor);
    expect(interceptor).toBeTruthy();
  });
});
