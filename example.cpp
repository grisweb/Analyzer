#include <stdio.h>
#include <math.h>

int main()
{
    long double x, y;

    printf("Enter x: ");
    scanf_s("%Lf", &x);

    printf("Enter y: ");
    scanf_s("%Lf", &y);

    long double output = (cbrt(6 * tan(3 * x)) + 9 * y) / (pow(x - y, 2));

    printf("Result = %llf", output);
}